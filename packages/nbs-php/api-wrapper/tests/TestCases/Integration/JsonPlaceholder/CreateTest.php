<?php

namespace Tests\TestCases\Integration\JsonPlaceholder;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\JsonPlaceholderPost;
use Tests\Helpers\MocksGuzzle;

class CreateTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::post('posts.create', 'https://jsonplaceholder.typicode.com/posts');
    }

    public function testCreate()
    {
        $data = [
            'title' => 'Inferno',
            'body' => 'Abandon all hope ye who enter here'
        ];
        $response = JsonPlaceholderPost::create($data);

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertTrue($response->hasAttribute('id'));
        $this->assertTrue($response->hasAttribute('title'));
        $this->assertEquals($data['title'], $response->title);
        $this->assertTrue($response->hasAttribute('body'));
        $this->assertEquals($data['body'], $response->body);
    }

    public function testStore()
    {
        $data = [
            'title' => 'Inferno',
            'body' => 'Abandon all hope ye who enter here'
        ];
        $resource = new JsonPlaceholderPost($data);
        $response = $resource->store();

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertTrue($response->hasAttribute('id'));
        $this->assertTrue($response->hasAttribute('title'));
        $this->assertEquals($data['title'], $response->title);
        $this->assertTrue($response->hasAttribute('body'));
        $this->assertEquals($data['body'], $response->body);
    }
}
