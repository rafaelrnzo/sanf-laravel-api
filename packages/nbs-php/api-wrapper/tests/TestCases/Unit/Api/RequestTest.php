<?php

namespace Tests\TestCases\Unit\Api;

use NbsPhp\ApiWrapper\Api\Endpoint;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Api\Processors\BasicPostProcessor;
use Tests\Assets\Api\Processors\BasicPreProcessor;
use Tests\Helpers\MocksGuzzle;

class RequestTest extends TestCase
{
    use MocksGuzzle;

    public function testRoute()
    {
        Route::get('requestRouteTest', 'requestRouteTest');
        $request = Request::route('requestRouteTest');

        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals('requestRouteTest', $request->getUrl());
    }

    public function testFluentBuilders()
    {
        $request = new Request(new Endpoint('GET', 'test'));

        $this->assertEquals($request, $request->pathParams([]));
        $this->assertEquals($request, $request->auth(['username', 'password']));
        $this->assertEquals($request, $request->headers([]));
        $this->assertEquals($request, $request->queryParams([]));
        $this->assertEquals($request, $request->body(''));
        $this->assertEquals($request, $request->json([]));
        $this->assertEquals($request, $request->formParams([]));
        $this->assertEquals($request, $request->multipart([]));
        $this->assertEquals($request, $request->options([]));
    }

    public function testMethod()
    {
        $request = new Request(new Endpoint('GET', 'test'));
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testUrl()
    {
        $endpoint = (new Endpoint('GET', 'test/{id}'))->baseUrl('https://www.example.com');
        $request = (new Request($endpoint))->pathParams(['id' => 1]);

        $this->assertEquals('https://www.example.com/test/1', $request->getUrl());
    }

    public function testOptions()
    {
        $endpoint = new Endpoint('GET', 'test');
        $request = new Request($endpoint);

        $auth = ['username', 'password'];
        $request->auth($auth);

        $headers = ['header' => 'value'];
        $request->headers($headers);

        $query = ['query' => 'value'];
        $request->queryParams($query);

        $body = 'body';
        $request->body($body);

        $json = ['json' => 'value'];
        $request->json($json);

        $form = ['form' => 'value'];
        $request->formParams($form);

        $multipart = ['multipart' => 'value'];
        $request->multipart($multipart);

        $extraOptions = ['option' => 'value'];
        $request->options($extraOptions);

        $options = $request->getOptions();
        $this->assertEquals($auth, $options['auth']);
        $this->assertEquals($headers, $options['headers']);
        $this->assertEquals($query, $options['query']);
        $this->assertEquals($body, $options['body']);
        $this->assertEquals($json, $options['json']);
        $this->assertEquals($form, $options['form_params']);
        $this->assertEquals($multipart, $options['multipart']);
        $this->assertEquals($extraOptions['option'], $options['option']);
    }

    public function testProcessorsCalledOnSend()
    {
        BasicPreProcessor::$called = false;
        BasicPostProcessor::$called = false;

        $endpoint = (new Endpoint('GET', 'test'))
            ->baseUrl('https://www.example.com')
            ->processor([BasicPreProcessor::class, BasicPostProcessor::class]);

        (new Request($endpoint, $this->getMockClient()))->send();

        $this->assertTrue(BasicPreProcessor::$called);
        $this->assertTrue(BasicPostProcessor::$called);
    }

    public function testSend()
    {
        Route::get('test', 'test');

        $response = Request::route('test', $this->getMockClient())->send();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
