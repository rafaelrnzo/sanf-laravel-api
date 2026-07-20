<?php

namespace Tests\Units\StandbyFinancing;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use Sanf\Api\Modules\StandbyFinancing\Controllers\SbfTransactionController;
use Sanf\Core\Modules\StandbyFinancing\Jobs\SendSbfSubmittedEmailJob;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfInvoiceCheckModel;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanBankAccountModel;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SbfTransactionControllerTest extends \TestCase
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

        require_once base_path('packages/sanf/core/database/migrations/2026_06_24_000001_create_sbf_invoice_checks_table.php');
        require_once base_path('packages/sanf/core/database/migrations/2026_06_24_000002_create_sbf_pengajuan_table.php');
        require_once base_path('packages/sanf/core/database/migrations/2026_07_14_000001_create_sbf_pengajuan_bank_accounts_table.php');

        (new \CreateSbfInvoiceChecksTable())->up();
        (new \CreateSbfPengajuanTable())->up();
        (new \CreateSbfPengajuanBankAccountsTable())->up();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        DB::disconnect('sqlite');

        parent::tearDown();
    }

    public function testCheckInvoiceIsSavedBeforeSuccessfulForward(): void
    {
        $payload = [
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-1',
            'nomor_invoice' => 'INV-1',
            'total_invoice' => 1500000,
        ];

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('checkInvoice')
            ->once()
            ->with($payload)
            ->andReturn(['message' => 'Eligible']);

        $response = (new SbfTransactionController())->checkInvoice(
            Request::create('/sbf/check-invoice', 'POST', $payload),
            $service
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', SbfInvoiceCheckModel::first()->core_status);
        $this->assertSame('Eligible', SbfInvoiceCheckModel::first()->core_message);
    }

    public function testCheckInvoiceReturnsOriginalCoreMessage(): void
    {
        $payload = [
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-1',
            'nomor_invoice' => 'INV-1',
            'total_invoice' => 1500000,
        ];

        $coreResponse = new PsrResponse(422, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'error',
            'message' => 'Invoice sudah pernah diupload sebelumnya.',
            'errors' => null,
        ]));

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('checkInvoice')
            ->once()
            ->with($payload)
            ->andThrow(new ClientException(
                'Core rejected invoice',
                new PsrRequest('POST', '/api/standby_financing/check_invoice'),
                $coreResponse
            ));

        $response = (new SbfTransactionController())->checkInvoice(
            Request::create('/sbf/check-invoice', 'POST', $payload),
            $service
        );

        $body = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($body['success']);
        $this->assertSame('error', $body['status']);
        $this->assertSame('422', $body['code']);
        $this->assertSame('Invoice sudah pernah diupload sebelumnya.', $body['message']);
        $this->assertSame('Invoice sudah pernah diupload sebelumnya.', SbfInvoiceCheckModel::first()->core_message);
    }

    public function testCheckPeriodForwardsValidatedPayload(): void
    {
        $payload = [
            'cust_id' => '1020000341',
            'period_end' => '2026-07-22',
        ];

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->once()->with('1020000341')->andReturnSelf();
        $service->shouldReceive('checkPeriod')
            ->once()
            ->with($payload)
            ->andReturn([
                'status' => 'success',
                'message' => 'Periode valid dan dapat diproses.',
                'data' => $payload,
            ]);

        $response = (new SbfTransactionController())->checkPeriod(
            Request::create('/sbf/check-period', 'POST', $payload),
            $service
        );

        $body = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $body['status']);
        $this->assertSame('Periode valid dan dapat diproses.', $body['data']['message']);
    }

    public function testCheckPeriodReturnsOriginalCoreMessage(): void
    {
        $payload = [
            'cust_id' => '1020000341',
            'period_end' => '2026-07-22',
        ];

        $coreResponse = new PsrResponse(422, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'error',
            'message' => 'Periode ini sudah pernah disubmit untuk customer tersebut.',
        ]));

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->once()->with('1020000341')->andReturnSelf();
        $service->shouldReceive('checkPeriod')
            ->once()
            ->with($payload)
            ->andThrow(new ClientException(
                'Core rejected period',
                new PsrRequest('POST', '/api/standby_financing/check_period'),
                $coreResponse
            ));

        $response = (new SbfTransactionController())->checkPeriod(
            Request::create('/sbf/check-period', 'POST', $payload),
            $service
        );

        $body = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($body['success']);
        $this->assertSame('error', $body['status']);
        $this->assertSame('422', $body['code']);
        $this->assertSame('Periode ini sudah pernah disubmit untuk customer tersebut.', $body['message']);
    }

    public function testFailedPengajuanRemainsAvailableForRetry(): void
    {
        $payload = $this->pengajuanPayload();
        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('submitPengajuan')
            ->once()
            ->with($payload)
            ->andThrow(new \RuntimeException('Core unavailable'));

        $response = (new SbfTransactionController())->submitPengajuan(
            Request::create('/sbf/pengajuan', 'POST', $payload),
            $service
        );

        $record = SbfPengajuanModel::first();

        $this->assertSame(502, $response->getStatusCode());
        $this->assertSame('failed', $record->local_status);
        $this->assertSame('error', $record->core_status);
        $this->assertSame(1, $record->total_invoice_count);
        $this->assertSame(1500000, $record->total_amount);
        $this->assertSame(1, SbfPengajuanBankAccountModel::where('pengajuan_id', $record->id)->count());
    }

    public function testSubmitPengajuanReturnsOriginalCoreMessage(): void
    {
        $payload = $this->pengajuanPayload();
        $coreResponse = new PsrResponse(422, ['Content-Type' => 'application/json'], json_encode([
            'status' => 'error',
            'message' => 'Pengajuan untuk periode ini sudah pernah disubmit.',
            'errors' => null,
        ]));

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('submitPengajuan')
            ->once()
            ->with($payload)
            ->andThrow(new ClientException(
                'Core rejected pengajuan',
                new PsrRequest('POST', '/api/standby_financing/store'),
                $coreResponse
            ));

        $response = (new SbfTransactionController())->submitPengajuan(
            Request::create('/sbf/pengajuan', 'POST', $payload),
            $service
        );

        $body = $response->getData(true);
        $record = SbfPengajuanModel::first();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($body['success']);
        $this->assertSame('error', $body['status']);
        $this->assertSame('422', $body['code']);
        $this->assertSame('Pengajuan untuk periode ini sudah pernah disubmit.', $body['message']);
        $this->assertSame('failed', $record->local_status);
        $this->assertSame('error', $record->core_status);
        $this->assertSame('Pengajuan untuk periode ini sudah pernah disubmit.', $record->core_message);
    }

    public function testPengajuanStoresMultipleBankAccountsWhileForwardingCoreCompatiblePayload(): void
    {
        $payload = $this->pengajuanPayload();
        unset($payload['bank_account']);
        $payload['bank_accounts'] = [
            [
                'bank_id' => '1',
                'bank_owner' => 'PT Supplier',
                'bank_provider' => 'Bank Example',
                'bank_account_number' => '1234567890',
            ],
            [
                'bank_id' => '2',
                'bank_owner' => 'PT Supplier Dua',
                'bank_provider' => 'Bank Example Dua',
                'bank_account_number' => '9876543210',
            ],
        ];

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('submitPengajuan')
            ->once()
            ->with(Mockery::on(function (array $corePayload) {
                return !isset($corePayload['bank_accounts'])
                    && $corePayload['bank_account']['bank_id'] === '1'
                    && $corePayload['bank_account']['bank_account_number'] === '1234567890';
            }))
            ->andReturn(['status' => 'success', 'data' => ['recap_id_b2b' => 'REC-1']]);

        $response = (new SbfTransactionController())->submitPengajuan(
            Request::create('/sbf/pengajuan', 'POST', $payload),
            $service
        );

        $record = SbfPengajuanModel::first();
        $bankAccounts = SbfPengajuanBankAccountModel::where('pengajuan_id', $record->id)
            ->orderBy('bank_id')
            ->get();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('submitted', $record->local_status);
        $this->assertSame('REC-1', $record->core_recap_id);
        $this->assertCount(2, $bankAccounts);
        $this->assertSame('1', $bankAccounts[0]->bank_id);
        $this->assertSame('2', $bankAccounts[1]->bank_id);
    }

    public function testSubmitPengajuanDispatchesSubmittedEmailForCustomerAndAdmin(): void
    {
        Queue::fake();

        config(['sanf-mobile.mail_to_admin' => 'admin-1@sanf.co.id, admin-2@sanf.co.id']);

        $payload = $this->pengajuanPayload();
        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('submitPengajuan')
            ->once()
            ->with($payload)
            ->andReturn(['status' => 'success', 'data' => ['recap_id_b2b' => 'REC-1']]);

        $response = (new SbfTransactionController())->submitPengajuan(
            Request::create('/sbf/pengajuan', 'POST', $payload),
            $service
        );

        $this->assertSame(200, $response->getStatusCode());

        Queue::assertPushed(SendSbfSubmittedEmailJob::class, 2);
        Queue::assertPushed(SendSbfSubmittedEmailJob::class, function ($job) {
            $recipients = $this->getJobProtectedProperty($job, 'recipients');
            $data = $this->getJobProtectedProperty($job, 'data');

            return $recipients === null
                && $data['cust_id'] === 'CUST-1'
                && $data['recap_id_b2b'] === 'REC-1'
                && $data['total_invoice_count'] === 1
                && $data['total_amount'] === 1500000;
        });
        Queue::assertPushed(SendSbfSubmittedEmailJob::class, function ($job) {
            $recipients = $this->getJobProtectedProperty($job, 'recipients');
            $data = $this->getJobProtectedProperty($job, 'data');

            return $recipients === ['admin-1@sanf.co.id', 'admin-2@sanf.co.id']
                && $data['cust_id'] === 'CUST-1'
                && $data['recap_id_b2b'] === 'REC-1';
        });
    }

    public function testSubmitPengajuanUsesNestedCoreRecapIdForSubmittedEmail(): void
    {
        Queue::fake();

        config(['sanf-mobile.mail_to_admin' => '']);

        $payload = $this->pengajuanPayload();
        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->andReturnSelf();
        $service->shouldReceive('submitPengajuan')
            ->once()
            ->with($payload)
            ->andReturn([
                'status' => 'success',
                'data' => [
                    'status' => 'success',
                    'message' => 'Pengajuan standby financing berhasil disimpan',
                    'data' => ['recap_id_b2b' => 'SF26070013'],
                ],
            ]);

        $response = (new SbfTransactionController())->submitPengajuan(
            Request::create('/sbf/pengajuan', 'POST', $payload),
            $service
        );

        $record = SbfPengajuanModel::first();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('SF26070013', $record->core_recap_id);
        Queue::assertPushed(SendSbfSubmittedEmailJob::class, function ($job) {
            $data = $this->getJobProtectedProperty($job, 'data');

            return $data['recap_id_b2b'] === 'SF26070013';
        });
    }

    public function testUploadDocumentStoresFileToMinioAndReturnsWebCompatibleResponse(): void
    {
        Storage::fake('minio_post');

        $file = UploadedFile::fake()->create('invoice-asli.pdf', 120, 'application/pdf');

        $request = Request::create('/sbf/document/upload', 'POST', [
            'cust_id' => 'CUST-1',
        ], [], ['file' => $file]);

        $response = (new SbfTransactionController())->uploadDocument($request);

        $this->assertSame(200, $response->getStatusCode());

        $body = $response->getData(true);
        $data = $body['data'];

        $this->assertSame('success', $body['status']);
        $this->assertSame('invoice-asli.pdf', $data['origin_name']);
        $this->assertStringStartsWith('SBF-', $data['xid']);
        $this->assertStringStartsWith('uploads/sbf/' . date('Y/m') . '/', $data['path']);
        $this->assertStringEndsWith('.pdf', $data['file_name']);
        $this->assertStringEndsWith($data['file_name'], $data['path']);

        Storage::disk('minio_post')->assertExists($data['path']);
    }

    public function testUploadDocumentRejectsMissingFile(): void
    {
        Storage::fake('minio_post');

        $request = Request::create('/sbf/document/upload', 'POST', ['cust_id' => 'CUST-1']);

        $this->expectException(ValidationException::class);

        (new SbfTransactionController())->uploadDocument($request);
    }

    public function testUploadDocumentRejectsUnsupportedExtension(): void
    {
        Storage::fake('minio_post');

        $file = UploadedFile::fake()->create('malware.exe', 10, 'application/octet-stream');

        $request = Request::create('/sbf/document/upload', 'POST', [
            'cust_id' => 'CUST-1',
        ], [], ['file' => $file]);

        $this->expectException(ValidationException::class);

        (new SbfTransactionController())->uploadDocument($request);
    }

    private function pengajuanPayload(): array
    {
        return [
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-1',
            'period_start' => '2026-06-24',
            'period_end' => '2026-07-24',
            'tenor' => 30,
            'supplier' => [[
                'supplier_id' => 'SUP-1',
                'total_invoice' => 1,
                'total_amount' => 1500000,
                'invoice_list' => [[
                    'nomor_invoice' => 'INV-1',
                    'tanggal_invoice' => '2026-06-20',
                    'currency' => 'IDR',
                    'amount' => 1500000,
                ]],
            ]],
            'bank_account' => [
                'bank_id' => '1',
                'bank_owner' => 'PT Supplier',
                'bank_provider' => 'Bank Example',
                'bank_account_number' => '1234567890',
            ],
            'invoice_document' => [['file_path' => 'invoice/inv-1.pdf']],
            'spt_dokuments' => ['file_path' => 'spt/spt.pdf'],
            'supporting_dokuments' => [],
        ];
    }

    private function getJobProtectedProperty(object $job, string $property)
    {
        $reflection = new \ReflectionClass($job);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($job);
    }
}
