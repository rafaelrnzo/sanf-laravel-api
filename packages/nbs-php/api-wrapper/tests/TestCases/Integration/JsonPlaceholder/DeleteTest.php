<?php

namespace Tests\TestCases\Integration\JsonPlaceholder;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\JsonPlaceholderPost;
use Tests\Helpers\MocksGuzzle;

class DeleteTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::delete('posts.delete', 'https://jsonplaceholder.typicode.com/posts/{id}');
    }

    public function testDelete()
    {
        JsonPlaceholderPost::delete(1);
        $this->assertTrue(true);
    }

    public function testDestroy()
    {
        $data = [
            'id' => 1
        ];
        $resource = new JsonPlaceholderPost($data);
        $resource->destroy();
        $this->assertTrue(true);
    }
}
