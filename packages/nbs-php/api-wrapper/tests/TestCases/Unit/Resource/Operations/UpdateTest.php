<?php

namespace Tests\TestCases\Unit\Resource\Operations;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\BasicApiResource;
use Tests\Helpers\MocksGuzzle;

class UpdateTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::patch('basicApiResources.update', '/basicApiResources/{id}');
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned.
     */
    public function testUpdate()
    {
        $data = [
            'id' => 1,
            'name' => 'George Burdell',
        ];
        $json = json_encode($data);
        $response = BasicApiResource::update(1, $data, [], $this->getMockClient(200, $json));

        $this->assertInstanceOf(BasicApiResource::class, $response);
        $this->assertEquals($data, $response->attributes);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (same instance).
     */
    public function testUpdateAttributes()
    {
        $original = [
            'id' => 1,
            'is_admin' => false,
        ];
        $data = [
            'id' => 1,
            'name' => 'George Burdell',
        ];
        $json = json_encode($data);
        $resource = new BasicApiResource($original);
        $response = $resource->updateAttributes($data, [], $this->getMockClient(200, $json));

        $this->assertInstanceOf(BasicApiResource::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertEquals(array_merge($original, $data), $response->attributes);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (same instance).
     */
    public function testSaveChanges()
    {
        $original = [
            'id' => 1,
            'is_admin' => false,
        ];
        $data = [
            'id' => 1,
            'name' => 'George Burdell',
        ];
        $json = json_encode($data);
        $resource = new BasicApiResource($original);
        $resource->mergeAttributes($data);
        $response = $resource->saveChanges([], $this->getMockClient(200, $json));

        $this->assertInstanceOf(BasicApiResource::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertEquals(array_merge($original, $data), $response->attributes);
    }
}
