<?php

namespace Tests\Units\StandbyFinancing;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request as HttpRequest;
use PHPUnit\Framework\TestCase;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SanfApiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'sanf-api-v2.client_id' => 'cid',
            'sanf-api-v2.client_secret' => 'csecret',
        ]);

        // The SanfCoreApiProcessorV2 reads the inbound request for the X-Request-ID header.
        app()->instance('request', HttpRequest::create('/sbf', 'GET'));
    }

    public function testItRelaysPlafondListWithCustomerQueryAndBasicAuth(): void
    {
        $history = [];
        $service = $this->service([new Response(200, [], '{"status":"success","data":[]}')], $history);

        $service->setUser('8624PROSM')->getPlafondListSbf('8624PROSM');

        $request = $history[0]['request'];

        $this->assertStringContainsString('api/plafond/list_sbf', $request->getUri()->getPath());
        $this->assertSame('cust_id=8624PROSM', $request->getUri()->getQuery());
        $this->assertSame(
            'Basic ' . base64_encode('cid;csecret;8624PROSM'),
            $request->getHeaderLine('Authorization')
        );
    }

    public function testItForwardsCheckInvoiceUsingCoreFieldNames(): void
    {
        $history = [];
        $service = $this->service([new Response(200, [], '{"status":"success"}')], $history);

        $service->setUser('CUST-1')->checkInvoice([
            'cust_id' => 'CUST-1',
            'no_plafond' => '62505004136',
            'nomor_invoice' => '1234567890',
            'total_invoice' => 500000,
        ]);

        $request = $history[0]['request'];

        $this->assertStringContainsString('api/standby_financing/check_invoice', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true);

        $this->assertSame([
            'nomor_invoice' => '1234567890',
            'total_invoice' => 500000,
            'noplafond' => '62505004136',
        ], $body);
        $this->assertArrayNotHasKey('cust_id', $body);
        $this->assertArrayNotHasKey('no_plafond', $body);
    }

    public function testItForwardsCheckPeriodToCoreContract(): void
    {
        $history = [];
        $service = $this->service([new Response(200, [], '{"status":"success"}')], $history);

        $service->setUser('1020000341')->checkPeriod([
            'cust_id' => '1020000341',
            'period_end' => '2026-07-22',
        ]);

        $request = $history[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringContainsString('api/standby_financing/check_period', $request->getUri()->getPath());
        $this->assertSame(
            'Basic ' . base64_encode('cid;csecret;1020000341'),
            $request->getHeaderLine('Authorization')
        );

        $this->assertSame([
            'cust_id' => '1020000341',
            'period_end' => '2026-07-22',
        ], json_decode((string) $request->getBody(), true));
    }

    public function testItForwardsPengajuanToStoreMappedToCoreContract(): void
    {
        $history = [];
        $service = $this->service([new Response(200, [], '{"status":"success","data":{"recap_id":"REC-1"}}')], $history);

        $service->setUser('CUST-1')->submitPengajuan([
            'cust_id' => 'CUST-1',
            'no_plafond' => '62505004136',
            'period_start' => '2026-05-03',
            'period_end' => '2026-05-29',
            'tenor' => 12,
            'supplier' => [[
                'supplier_id' => '0000000073',
                'total_invoice' => 2,
                'total_amount' => 85000000,
                'invoice_list' => [[
                    'nomor_invoice' => 'INV/2026/05/0011',
                    'tanggal_invoice' => '2026-05-05',
                    'currency' => 'IDR',
                    'amount' => 85000000,
                ]],
            ]],
            'bank_account' => [
                'bank_id' => '0001101',
                'bank_owner' => 'PT. SUKSES TUNGGAL MANDIRI',
                'bank_provider' => 'BANK BCA',
                'bank_account_number' => '883.059.1533',
            ],
            'invoice_document' => [['file_path' => 'po.pdf', 'file_name' => 'po.pdf']],
            'spt_dokuments' => ['file_path' => 'spt.pdf', 'file_name' => 'spt.pdf'],
            'supporting_dokuments' => [],
        ]);

        $request = $history[0]['request'];

        $this->assertSame('POST', $request->getMethod());
        $this->assertStringContainsString('api/standby_financing/store', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true);

        $this->assertArrayNotHasKey('cust_id', $body);
        $this->assertSame([
            'bank_id' => '0001101',
            'owner' => 'PT. SUKSES TUNGGAL MANDIRI',
            'provider' => 'BANK BCA',
            'account_number' => '883.059.1533',
            'total_amount' => '85000000',
        ], $body['bank_account']);
    }

    public function testItForwardsFirstBankAccountWhenPayloadUsesBankAccountsList(): void
    {
        $history = [];
        $service = $this->service([new Response(200, [], '{"status":"success","data":{"recap_id":"REC-1"}}')], $history);

        $service->setUser('CUST-1')->submitPengajuan([
            'cust_id' => 'CUST-1',
            'no_plafond' => '62505004136',
            'period_start' => '2026-05-03',
            'period_end' => '2026-05-29',
            'tenor' => 12,
            'supplier' => [[
                'supplier_id' => '0000000073',
                'total_invoice' => 2,
                'total_amount' => 85000000,
                'invoice_list' => [[
                    'nomor_invoice' => 'INV/2026/05/0011',
                    'tanggal_invoice' => '2026-05-05',
                    'currency' => 'IDR',
                    'amount' => 85000000,
                ]],
            ]],
            'bank_accounts' => [
                [
                    'bank_id' => '0001101',
                    'bank_owner' => 'PT. SUKSES TUNGGAL MANDIRI',
                    'bank_provider' => 'BANK BCA',
                    'bank_account_number' => '883.059.1533',
                ],
                [
                    'bank_id' => '0002202',
                    'bank_owner' => 'PT. SUKSES TUNGGAL MANDIRI 2',
                    'bank_provider' => 'BANK MANDIRI',
                    'bank_account_number' => '9988776655',
                ],
            ],
            'invoice_document' => [['file_path' => 'po.pdf', 'file_name' => 'po.pdf']],
            'spt_dokuments' => ['file_path' => 'spt.pdf', 'file_name' => 'spt.pdf'],
            'supporting_dokuments' => [],
        ]);

        $body = json_decode((string) $history[0]['request']->getBody(), true);

        $this->assertSame([
            'bank_id' => '0001101',
            'owner' => 'PT. SUKSES TUNGGAL MANDIRI',
            'provider' => 'BANK BCA',
            'account_number' => '883.059.1533',
            'total_amount' => '85000000',
        ], $body['bank_account']);
        $this->assertArrayNotHasKey('bank_accounts', $body);
    }

    private function service(array $responses, array &$history = []): SanfApiService
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($history));

        return new SanfApiService(new Client(['handler' => $stack]));
    }
}
