<?php

namespace Tests\Units\StandbyFinancing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;
use Sanf\Api\Modules\StandbyFinancing\Controllers\SbfTransactionController;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfInvoiceCheckModel;
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

        (new \CreateSbfInvoiceChecksTable())->up();
        (new \CreateSbfPengajuanTable())->up();
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
}
