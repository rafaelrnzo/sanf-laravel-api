<?php

namespace Tests\TestCases\Integration\JsonPlaceholder;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\JsonPlaceholderPost;
use Tests\Helpers\MocksGuzzle;

class UpdateTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::patch('posts.update', 'https://jsonplaceholder.typicode.com/posts/{id}');
    }

    public function testUpdate()
    {
        $data = [
            'title' => 'Inferno'
        ];
        $response = JsonPlaceholderPost::update(1, $data);

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertEquals($data['title'], $response->title);
    }

    public function testUpdateAttributes()
    {
        $data = [
            'title' => 'inferno'
        ];
        $resource = new JsonPlaceholderPost([
            'id' => 1
        ]);
        $response = $resource->updateAttributes($data);

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertEquals($data['title'], $response->title);
    }

    public function testSaveChanges()
    {
        $newTitle = 'Inferno';
        $resource = new JsonPlaceholderPost([
            'id' => 1
        ]);
        $resource->title = $newTitle;
        $response = $resource->saveChanges();

        $this->assertInstanceOf(JsonPlaceholderPost::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertEquals($newTitle, $response->title);
    }
}
