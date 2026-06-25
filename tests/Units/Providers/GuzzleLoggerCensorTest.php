<?php

namespace Tests\Units\Providers;

use NbsPhp\Core\Providers\GuzzleLoggerServiceProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class GuzzleLoggerCensorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'guzzle-logger.censor.replacement' => '**censor**',
            'guzzle-logger.censor.bad-keys' => ['authorization', 'password', 'x-api-key', 'account_number'],
        ]);
    }

    public function testItCensorsSensitiveHeadersAndKeepsTheRest(): void
    {
        $headers = [
            'Authorization' => ['Bearer secret-token'],
            'Accept' => ['application/json'],
        ];

        $result = $this->invoke('censor', $headers);

        $this->assertSame('**censor**', $result['Authorization'][0]);
        $this->assertSame('application/json', $result['Accept'][0]);
    }

    public function testItCensorsSensitiveJsonBodyButLeavesNonJsonUntouched(): void
    {
        $censored = $this->invoke('censorBody', json_encode(['username' => 'user', 'password' => 'secret']));

        $this->assertSame('**censor**', $censored['password']);
        $this->assertSame('user', $censored['username']);

        $this->assertSame('<html>error</html>', $this->invoke('censorBody', '<html>error</html>'));
    }

    public function testItCensorsFinancialAccountNumberInNestedBody(): void
    {
        $censored = $this->invoke('censorBody', json_encode([
            'bank_account' => ['account_number' => '883.059.1533', 'provider' => 'BANK BCA'],
        ]));

        $this->assertSame('**censor**', $censored['bank_account']['account_number']);
        $this->assertSame('BANK BCA', $censored['bank_account']['provider']);
    }

    private function invoke(string $method, $argument)
    {
        $reflection = new ReflectionMethod(GuzzleLoggerServiceProvider::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke(new GuzzleLoggerServiceProvider(app()), $argument);
    }
}
