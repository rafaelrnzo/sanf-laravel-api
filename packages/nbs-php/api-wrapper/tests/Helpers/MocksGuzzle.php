<?php

namespace Tests\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

trait MocksGuzzle
{
    protected function getMockClient(int $status = 200, string $body = null): Client
    {
        return new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response($status, [], $body)
        ]))]);
    }
}