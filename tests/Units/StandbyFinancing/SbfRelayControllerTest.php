<?php

namespace Tests\Units\StandbyFinancing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;
use Sanf\Api\Modules\StandbyFinancing\Controllers\SbfRelayController;
use Sanf\Core\Modules\StandbyFinancing\Models\SbfPengajuanModel;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SbfRelayControllerTest extends \TestCase
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

        (new \CreateSbfPengajuanTable())->up();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        DB::disconnect('sqlite');

        parent::tearDown();
    }

    public function testPencairanDetailUsesLocalDocumentCategories(): void
    {
        SbfPengajuanModel::create([
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-1',
            'period_start' => '2026-07-15',
            'period_end' => '2026-08-15',
            'tenor' => 1,
            'total_invoice_count' => 3,
            'total_amount' => 23000000,
            'bank_id' => 'BANK-1',
            'bank_owner' => 'PT Example',
            'bank_provider' => 'Bank Example',
            'bank_account_number' => '1234567890',
            'supplier_payload' => [],
            'invoice_document' => [[
                'file_path' => 'uploads/sbf/2026/07/invoice.pdf',
                'file_name' => 'Invoice.pdf',
            ]],
            'spt_dokument' => [
                'file_path' => 'uploads/sbf/2026/07/spt.pdf',
                'file_name' => 'SPT.pdf',
            ],
            'supporting_dokuments' => [[
                'doc_id' => '002',
                'doc_desc' => 'Validasi',
                'file_path' => 'uploads/sbf/2026/07/supporting.pdf',
                'file_name' => 'Supporting.pdf',
            ]],
            'local_status' => 'submitted',
            'core_recap_id' => 'SF26070004',
            'core_status' => 'success',
        ]);

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->once()->with('CUST-1')->andReturnSelf();
        $service->shouldReceive('getDetailPencairan')
            ->once()
            ->with('SF26070004')
            ->andReturn([
                'status' => 'success',
                'data' => [
                    'recap_id_b2b' => 'SF26070004',
                    'invoice_document' => [
                        ['file_path' => 'uploads/sbf/2026/07/invoice.pdf', 'file_name' => 'Invoice.pdf'],
                        ['file_path' => 'uploads/sbf/2026/07/spt.pdf', 'file_name' => 'SPT.pdf'],
                        ['file_path' => 'uploads/sbf/2026/07/supporting.pdf', 'file_name' => 'Supporting.pdf'],
                    ],
                    'spt_dokuments' => null,
                    'supporting_dokuments' => [],
                ],
            ]);

        $response = (new SbfRelayController())->pencairanDetail(
            'SF26070004',
            Request::create('/sbf/pencairan/SF26070004', 'GET', ['cust_id' => 'CUST-1']),
            $service
        );

        $body = $response->getData(true);

        $this->assertSame('success', $body['status']);
        $this->assertCount(1, $body['data']['invoice_document']);
        $this->assertSame('Invoice.pdf', $body['data']['invoice_document'][0]['file_name']);
        $this->assertSame('SPT.pdf', $body['data']['spt_dokuments']['file_name']);
        $this->assertCount(1, $body['data']['supporting_dokuments']);
        $this->assertSame('Supporting.pdf', $body['data']['supporting_dokuments'][0]['file_name']);
    }

    public function testPencairanDetailKeepsCoreDocumentsWhenLocalRecordHasNoDocuments(): void
    {
        SbfPengajuanModel::create([
            'cust_id' => 'CUST-1',
            'no_plafond' => 'PLF-1',
            'period_start' => '2026-07-15',
            'period_end' => '2026-08-15',
            'tenor' => 1,
            'total_invoice_count' => 3,
            'total_amount' => 23000000,
            'bank_id' => 'BANK-1',
            'bank_owner' => 'PT Example',
            'bank_provider' => 'Bank Example',
            'bank_account_number' => '1234567890',
            'supplier_payload' => [],
            'invoice_document' => [],
            'spt_dokument' => [],
            'supporting_dokuments' => null,
            'local_status' => 'submitted',
            'core_recap_id' => 'SF26070005',
            'core_status' => 'success',
        ]);

        $service = Mockery::mock(SanfApiService::class);
        $service->shouldReceive('setUser')->once()->with('CUST-1')->andReturnSelf();
        $service->shouldReceive('getDetailPencairan')
            ->once()
            ->with('SF26070005')
            ->andReturn([
                'status' => 'success',
                'data' => [
                    'recap_id_b2b' => 'SF26070005',
                    'invoice_document' => [[
                        'file_path' => 'uploads/sbf/2026/07/core-invoice.pdf',
                        'file_name' => 'Core Invoice.pdf',
                    ]],
                    'supporting_dokuments' => [],
                ],
            ]);

        $response = (new SbfRelayController())->pencairanDetail(
            'SF26070005',
            Request::create('/sbf/pencairan/SF26070005', 'GET', ['cust_id' => 'CUST-1']),
            $service
        );

        $body = $response->getData(true);

        $this->assertSame('Core Invoice.pdf', $body['data']['invoice_document'][0]['file_name']);
    }
}
