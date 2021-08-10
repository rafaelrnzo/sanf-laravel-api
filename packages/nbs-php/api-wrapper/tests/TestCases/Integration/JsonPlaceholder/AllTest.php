<?php

namespace Tests\TestCases\Integration\JsonPlaceholder;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\JsonPlaceholderPost;

class AllTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::get('posts.all', 'https://jsonplaceholder.typicode.com/posts');
    }

    /**
     * Expected Behavior:
     * - Array of ApiResource instances with correct attributes returned
     */
    public function testAll()
    {
        $response = JsonPlaceholderPost::all();

        $this->assertCount(100, $response);

        foreach ($response as $post) {
            $this->assertInstanceOf(JsonPlaceholderPost::class, $post);
        }
    }
}
