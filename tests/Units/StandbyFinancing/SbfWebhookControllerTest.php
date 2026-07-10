<?php

namespace Tests\Units\StandbyFinancing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Mockery;
use Sanf\Api\Middleware\VerifyCoreWebhookSignature;
use Sanf\Api\Modules\StandbyFinancing\Controllers\SbfWebhookController;
use Sanf\Core\Modules\Log\Models\WebhookLogModel;
use Sanf\Core\Modules\StandbyFinancing\Jobs\SendSbfStatusChangedEmailJob;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;
use Sanf\Core\Modules\StandbyFinancing\UseCases\ProcessSbfStatusWebhookUseCase;

class SbfWebhookControllerTest extends \TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        require_once base_path('packages/sanf/core/database/migrations/2026_06_24_000002_create_sbf_pengajuan_table.php');
        require_once base_path('packages/sanf/core/database/migrations/2025_11_08_020900_create_webhook_log_table.php');

        (new \CreateSbfPengajuanTable())->up();
        (new \CreateWebhookLogTable())->up();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        DB::disconnect('sqlite');

        parent::tearDown();
    }

    private function validPayload(): array
    {
        return [
            'event_id' => 'evt_REC1_C_1782900000',
            'event_type' => 'standby_financing.status_changed',
            'data' => [
                'status' => 'C',
                'recap_id_b2b' => 'REC-1',
                'client_xid' => '1020000341',
            ],
        ];
    }

    private function seedPengajuan(string $recapId = 'REC-1', string $custId = 'CUST-1'): SbfPengajuanModel
    {
        return SbfPengajuanModel::create([
            'cust_id' => $custId,
            'no_plafond' => 'PLF-1',
            'period_start' => '2026-06-01',
            'period_end' => '2026-07-01',
            'tenor' => 30,
            'total_invoice_count' => 3,
            'total_amount' => 5000000,
            'bank_id' => '1',
            'bank_owner' => 'PT Example',
            'bank_provider' => 'Bank Example',
            'bank_account_number' => '1234567890',
            'supplier_payload' => [],
            'invoice_document' => [],
            'spt_dokument' => [],
            'local_status' => 'submitted',
            'core_recap_id' => $recapId,
            'core_status' => 'pending',
        ]);
    }

    public function testControllerRejectsMissingEventId(): void
    {
        $payload = $this->validPayload();
        unset($payload['event_id']);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $payload);

        $useCase = Mockery::mock(ProcessSbfStatusWebhookUseCase::class);
        $useCase->shouldNotReceive('handle');

        $this->expectException(ValidationException::class);

        (new SbfWebhookController())->handleStatus($request, $useCase);
    }

    public function testControllerRejectsMissingDataStatus(): void
    {
        $payload = $this->validPayload();
        unset($payload['data']['status']);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $payload);

        $useCase = Mockery::mock(ProcessSbfStatusWebhookUseCase::class);
        $useCase->shouldNotReceive('handle');

        $this->expectException(ValidationException::class);

        (new SbfWebhookController())->handleStatus($request, $useCase);
    }

    public function testControllerRejectsMissingRecapId(): void
    {
        $payload = $this->validPayload();
        unset($payload['data']['recap_id_b2b']);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $payload);

        $useCase = Mockery::mock(ProcessSbfStatusWebhookUseCase::class);
        $useCase->shouldNotReceive('handle');

        $this->expectException(ValidationException::class);

        (new SbfWebhookController())->handleStatus($request, $useCase);
    }

    public function testControllerAcceptsValidPayloadAndReturnsProcessed(): void
    {
        $this->seedPengajuan();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $this->validPayload());

        $response = (new SbfWebhookController())->handleStatus($request, $useCase);

        $this->assertSame(200, $response->getStatusCode());

        $body = $response->getData(true);
        $this->assertSame('Success', $body['message']);
        $this->assertSame('processed', $body['data']['result']);
    }

    public function testDuplicateEventIdReturnsDuplicate(): void
    {
        $this->seedPengajuan();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $payload = $this->validPayload();

        $request1 = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $payload);
        $response1 = (new SbfWebhookController())->handleStatus($request1, $useCase);
        $this->assertSame('processed', $response1->getData(true)['data']['result']);

        $request2 = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $payload);
        $response2 = (new SbfWebhookController())->handleStatus($request2, $useCase);
        $this->assertSame('duplicate', $response2->getData(true)['data']['result']);
    }

    public function testUseCaseUpdatesCoreStatusOnPengajuanModel(): void
    {
        $this->seedPengajuan();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $result = $useCase->handle($this->validPayload());

        $this->assertSame('processed', $result);

        $pengajuan = SbfPengajuanModel::where('core_recap_id', 'REC-1')->first();
        $this->assertSame('C', $pengajuan->core_status);
        $this->assertSame('Pengajuan Anda telah dikonfirmasi dan sedang diproses.', $pengajuan->core_message);
        $this->assertIsArray($pengajuan->core_response);
        $this->assertSame('evt_REC1_C_1782900000', $pengajuan->core_response['event_id']);
    }

    public function testUseCaseDispatchesEmailJob(): void
    {
        Queue::fake();

        $this->seedPengajuan();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $useCase->handle($this->validPayload());

        Queue::assertPushed(SendSbfStatusChangedEmailJob::class, 1);

        Queue::assertPushed(SendSbfStatusChangedEmailJob::class, function ($job) {
            $data = $this->getJobProtectedProperty($job, 'data');

            return $data['status'] === 'C'
                && $data['recap_id_b2b'] === 'REC-1'
                && $data['cust_id'] === 'CUST-1'
                && $data['total_invoice_count'] === 3
                && $data['total_amount'] === 5000000;
        });
    }

    public function testDuplicateEventDoesNotDispatchSecondJob(): void
    {
        Queue::fake();

        $this->seedPengajuan();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $payload = $this->validPayload();

        $useCase->handle($payload);
        $useCase->handle($payload);

        Queue::assertPushed(SendSbfStatusChangedEmailJob::class, 1);
    }

    public function testUseCaseCreatesWebhookLogForIdempotency(): void
    {
        $this->seedPengajuan();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $useCase->handle($this->validPayload());

        $log = WebhookLogModel::where('xid', 'evt_REC1_C_1782900000')->first();
        $this->assertNotNull($log);
        $this->assertSame('sbf.status', $log->key);
        $this->assertSame('REC-1', $log->reference_id);
        $this->assertNotNull($log->processed_at);
    }

    public function testUseCaseHandlesAllStatusCodes(): void
    {
        $codes = [
            'C' => 'dikonfirmasi',
            '1' => 'valid',
            '2' => 'tidak valid',
            'X' => 'ditolak',
            '0' => 'ditinjau',
        ];

        foreach ($codes as $code => $label) {
            $this->seedPengajuan("REC-{$code}", "CUST-{$code}");

            $payload = [
                'event_id' => "evt_REC{$code}_{$code}_1782900000",
                'event_type' => 'standby_financing.status_changed',
                'data' => [
                    'status' => $code,
                    'recap_id_b2b' => "REC-{$code}",
                    'client_xid' => "1020000{$code}",
                ],
            ];

            $useCase = new ProcessSbfStatusWebhookUseCase();
            $result = $useCase->handle($payload);

            $this->assertSame('processed', $result);

            $pengajuan = SbfPengajuanModel::where('core_recap_id', "REC-{$code}")->first();
            $this->assertSame((string) $code, (string) $pengajuan->core_status);
            $this->assertStringContainsString($label, $pengajuan->core_message);
        }
    }

    public function testUnknownRecapIdStillProcessesAndDispatchesJob(): void
    {
        Queue::fake();

        $useCase = new ProcessSbfStatusWebhookUseCase();
        $result = $useCase->handle($this->validPayload());

        $this->assertSame('processed', $result);

        $log = WebhookLogModel::where('xid', 'evt_REC1_C_1782900000')->first();
        $this->assertNotNull($log);
        $this->assertNotNull($log->processed_at);

        Queue::assertPushed(SendSbfStatusChangedEmailJob::class, 1);
    }

    public function testEventIdLongerThan32CharactersIsRejectedByValidation(): void
    {
        $payload = $this->validPayload();
        $payload['event_id'] = str_repeat('x', 33);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', $payload);

        $useCase = Mockery::mock(ProcessSbfStatusWebhookUseCase::class);
        $useCase->shouldNotReceive('handle');

        $this->expectException(ValidationException::class);

        (new SbfWebhookController())->handleStatus($request, $useCase);
    }

    public function testMiddlewareRejectsRequestWithoutSecret(): void
    {
        config(['core-webhook.secret' => '']);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST');
        $nextCalled = false;

        $middleware = new VerifyCoreWebhookSignature();
        $response = $middleware->handle($request, function () use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertFalse($nextCalled);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testMiddlewareRejectsMissingSignatureHeader(): void
    {
        config(['core-webhook.secret' => 'test-secret']);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST');
        $nextCalled = false;

        $middleware = new VerifyCoreWebhookSignature();
        $response = $middleware->handle($request, function () use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertFalse($nextCalled);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testMiddlewareRejectsInvalidSignature(): void
    {
        config(['core-webhook.secret' => 'test-secret']);

        $body = json_encode($this->validPayload());

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', [], [], [], [
            'HTTP_X-Signature' => 'sha256=invalidhash',
        ], $body);

        $nextCalled = false;

        $middleware = new VerifyCoreWebhookSignature();
        $response = $middleware->handle($request, function () use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertFalse($nextCalled);
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testMiddlewareAcceptsValidSignature(): void
    {
        $secret = 'test-secret';
        config(['core-webhook.secret' => $secret]);

        $body = json_encode($this->validPayload());
        $signature = 'sha256=' . hash_hmac('sha256', $body, $secret);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', [], [], [], [
            'HTTP_X-Signature' => $signature,
        ], $body);

        $nextCalled = false;

        $middleware = new VerifyCoreWebhookSignature();
        $middleware->handle($request, function () use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertTrue($nextCalled);
    }

    public function testMiddlewareAcceptsSignatureWithoutPrefix(): void
    {
        $secret = 'test-secret';
        config(['core-webhook.secret' => $secret]);

        $body = json_encode($this->validPayload());
        $signature = hash_hmac('sha256', $body, $secret);

        $request = Request::create('/webhooks/core-api/standby-financing/status', 'POST', [], [], [], [
            'HTTP_X-Signature' => $signature,
        ], $body);

        $nextCalled = false;

        $middleware = new VerifyCoreWebhookSignature();
        $middleware->handle($request, function () use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertTrue($nextCalled);
    }

    private function getJobProtectedProperty($job, string $property): mixed
    {
        $reflection = new \ReflectionProperty($job, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($job);
    }
}
