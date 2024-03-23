<?php

namespace Tests\TestCases\Unit\Api;

use NbsPhp\ApiWrapper\Api\Endpoint;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Api\Processors\BasicPostProcessor;
use Tests\Assets\Api\Processors\BasicPreProcessor;

class RouteTest extends TestCase
{
    public static $callbackResponse = null;

    public function testBasicEndpoint()
    {
        $endpoint = Route::endpoint('GET', 'test', 'test')
            ->processor(BasicPreProcessor::class);

        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('GET', $endpoint->getMethod());
        $this->assertEquals('test', $endpoint->getUrl());
        $this->assertCount(1, $endpoint->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $endpoint->getProcessors());

        $endpoint->baseUrl('https://www.example.com');

        $this->assertEquals('https://www.example.com/test', $endpoint->getUrl());
    }

    public function testHeadEndpoint()
    {
        $endpoint = Route::head('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('HEAD', $endpoint->getMethod());
    }

    public function testGetEndpoint()
    {
        $endpoint = Route::get('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('GET', $endpoint->getMethod());
    }

    public function testPostEndpoint()
    {
        $endpoint = Route::post('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('POST', $endpoint->getMethod());
    }

    public function testPutEndpoint()
    {
        $endpoint = Route::put('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('PUT', $endpoint->getMethod());
    }

    public function testPatchEndpoint()
    {
        $endpoint = Route::patch('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('PATCH', $endpoint->getMethod());
    }

    public function testDeleteEndpoint()
    {
        $endpoint = Route::delete('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('DELETE', $endpoint->getMethod());
    }

    public function testOptionsEndpoint()
    {
        $endpoint = Route::options('test', 'test');
        $this->assertInstanceOf(Endpoint::class, $endpoint);
        $this->assertEquals('OPTIONS', $endpoint->getMethod());
    }

    public function testGroup()
    {
        self::$callbackResponse = null;

        Route::group('https://www.example.com', [BasicPreProcessor::class], function () {
            self::$callbackResponse = Route::post('test', 'test');
        });

        /** @var Endpoint $groupedEndpoint */
        $groupedEndpoint = self::$callbackResponse;

        $this->assertInstanceOf(Endpoint::class, $groupedEndpoint);
        $this->assertEquals('POST', $groupedEndpoint->getMethod());
        $this->assertEquals('https://www.example.com/test', $groupedEndpoint->getUrl());
        $this->assertCount(1, $groupedEndpoint->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $groupedEndpoint->getProcessors());

        $basicEndpoint = Route::post('test', 'test');

        $this->assertInstanceOf(Endpoint::class, $basicEndpoint);
        $this->assertEquals('POST', $basicEndpoint->getMethod());
        $this->assertCount(0, $basicEndpoint->getProcessors());
    }

    public function testNestedGroup()
    {
        self::$callbackResponse = [];

        Route::group('https://www.example.com', [BasicPreProcessor::class], function () {
            Route::group('https://www.heliumservices.com', [BasicPostProcessor::class], function () {
                self::$callbackResponse[] = Route::post('test', 'test');
            });

            self::$callbackResponse[] = Route::post('test2', 'test2');
        });

        /** @var Endpoint $groupedEndpoint */
        $groupedEndpoint = array_shift(self::$callbackResponse);

        $this->assertInstanceOf(Endpoint::class, $groupedEndpoint);
        $this->assertEquals('POST', $groupedEndpoint->getMethod());
        $this->assertEquals('https://www.heliumservices.com/test', $groupedEndpoint->getUrl());
        $this->assertCount(2, $groupedEndpoint->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $groupedEndpoint->getProcessors());
        $this->assertContains(BasicPostProcessor::class, $groupedEndpoint->getProcessors());

        /** @var Endpoint $groupedEndpoint2 */
        $groupedEndpoint2 = array_shift(self::$callbackResponse);

        $this->assertInstanceOf(Endpoint::class, $groupedEndpoint2);
        $this->assertEquals('POST', $groupedEndpoint2->getMethod());
        $this->assertEquals('https://www.example.com/test2', $groupedEndpoint2->getUrl());
        $this->assertCount(1, $groupedEndpoint2->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $groupedEndpoint2->getProcessors());
    }

    public function testFind()
    {
        $this->expectException(EndpointNotDefinedException::class);
        Route::find('newEndpoint');

        $endpoint = Route::get('newEndpoint', 'test');
        $this->assertEquals($endpoint, Route::find('test'));
    }
}
