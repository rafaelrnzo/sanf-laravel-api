<?php

namespace Tests\TestCases\Unit\Resource\Operations;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\BasicApiResource;
use Tests\Helpers\MocksGuzzle;

class DeleteTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::delete('basicApiResources.delete', '/basicApiResources/{id}');
    }

    /**
     * Expected Behavior:
     * - No exceptions are thrown.
     */
    public function testDelete()
    {
        BasicApiResource::delete(1, [], $this->getMockClient());
        $this->assertTrue(true);
    }

    /**
     * Expected Behavior:
     * - No exceptions are thrown.
     */
    public function testDestroy()
    {
        $data = [
            'id' => 1,
        ];
        $resource = new BasicApiResource($data);
        $resource->destroy([], $this->getMockClient());
        $this->assertTrue(true);
    }
}
