<?php

namespace Tests\TestCases\Integration\JsonPlaceholder;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\JsonPlaceholderPost;
use Tests\Helpers\MocksGuzzle;

class GetTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::get('posts.get', 'https://jsonplaceholder.typicode.com/posts/{id}');
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned
     */
    public function testGet()
    {
        $response = JsonPlaceholderPost::get(1);

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertEquals(1, $response->id);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (same instance)
     */
    public function testRefresh()
    {
        $original = [
            'id' => 1,
            'title' => 'Not the real title'
        ];
        $resource = new JsonPlaceholderPost($original);
        $response = $resource->refresh();

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertEquals(1, $response->id);
        $this->assertNotEquals($original['title'], $response->title);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (new instance)
     */
    public function testFresh()
    {
        $original = [
            'id' => 1,
            'title' => 'Not the real title'
        ];
        $resource = new JsonPlaceholderPost($original);
        $response = $resource->fresh();

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertNotEquals($resource, $response);
        $this->assertEquals(1, $response->id);
        $this->assertNotEquals($original['title'], $response->title);
    }
}
