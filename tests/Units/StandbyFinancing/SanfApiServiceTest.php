<?php

namespace Tests\Units\StandbyFinancing;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sanf\Integration\Modules\StandbyFinancing\SanfApiService;

class SanfApiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.sanf.base_url_mobile' => 'https://mobile.example.test/MobileAPI/index.php',
            'services.sanf.base_url_core' => 'https://core.example.test/api',
            'services.sanf.timeout' => 10,
            'services.sanf.auth_token' => 'test-token',
            'services.sanf.paths.check_invoice' => '/standby_financing/check_invoice',
            'services.sanf.paths.submit_pengajuan' => '/standby_financing/submit',
        ]);
    }

    public function testItRelaysPlafondListWithCustomerQuery(): void
    {
        $history = [];
        $service = $this->service(
            [new Response(200, [], '{"data":[{"no_plafond":"PLF-1"}]}')],
            $history
        );

        $response = $service->getPlafondListSbf('CUST-1');

        $this->assertSame('PLF-1', $response['data'][0]['no_plafond']);
        $this->assertSame(
            'https://core.example.test/api/plafond/list_sbf?cust_id=CUST-1',
            (string) $history[0]['request']->getUri()
        );
        $this->assertSame('Bearer test-token', $history[0]['request']->getHeaderLine('Authorization'));
    }

    public function testItForwardsPengajuanAsJson(): void
    {
        $history = [];
        $service = $this->service(
            [new Response(200, [], '{"message":"ok","data":{"recap_id":"REC-1"}}')],
            $history
        );

        $payload = ['no_plafond' => 'PLF-1'];
        $response = $service->submitPengajuan($payload);

        $this->assertSame('REC-1', $response['data']['recap_id']);
        $this->assertSame('POST', $history[0]['request']->getMethod());
        $this->assertSame(
            'https://core.example.test/api/standby_financing/submit',
            (string) $history[0]['request']->getUri()
        );
        $this->assertSame($payload, json_decode((string) $history[0]['request']->getBody(), true));
    }

    public function testItRejectsInvalidCoreJson(): void
    {
        $service = $this->service([new Response(200, [], '<html>error</html>')]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('invalid JSON');

        $service->getBankAccount('CUST-1');
    }

    private function service(array $responses, array &$history = []): SanfApiService
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($history));

        return new SanfApiService(new Client(['handler' => $stack]));
    }
}
