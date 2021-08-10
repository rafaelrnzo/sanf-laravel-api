<?php

namespace Tests\TestCases\Unit\Api;

use NbsPhp\ApiWrapper\Api\Endpoint;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Api\Processors\BasicPostProcessor;
use Tests\Assets\Api\Processors\BasicPreProcessor;

class EndpointTest extends TestCase
{
    public function testFluentBuilders()
    {
        $endpoint = new Endpoint('GET', 'test');

        $this->assertEquals($endpoint, $endpoint->baseUrl('https://www.example.com'));
        $this->assertEquals($endpoint, $endpoint->processor(BasicPreProcessor::class));
    }

    public function testGetMethod()
    {
        $endpoint = new Endpoint('GET', 'test');
        $this->assertEquals('GET', $endpoint->getMethod());
    }

    public function testGetUrl()
    {
        $endpoint = new Endpoint('GET', 'test');
        $this->assertEquals('test', $endpoint->getUrl());

        $endpoint->baseUrl('https://www.example.com');
        $this->assertEquals('https://www.example.com/test', $endpoint->getUrl());
    }

    public function testGetProcessors()
    {
        $endpoint = new Endpoint('GET', 'test');
        $this->assertEmpty($endpoint->getProcessors());

        $endpoint->processor(BasicPreProcessor::class);
        $this->assertCount(1, $endpoint->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $endpoint->getProcessors());

        //Test that adding the same processor twice has no effect
        $endpoint->processor(BasicPreProcessor::class);
        $this->assertCount(1, $endpoint->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $endpoint->getProcessors());

        $endpoint->processor([BasicPostProcessor::class]);
        $this->assertCount(2, $endpoint->getProcessors());
        $this->assertContains(BasicPreProcessor::class, $endpoint->getProcessors());
        $this->assertContains(BasicPostProcessor::class, $endpoint->getProcessors());
    }
}
