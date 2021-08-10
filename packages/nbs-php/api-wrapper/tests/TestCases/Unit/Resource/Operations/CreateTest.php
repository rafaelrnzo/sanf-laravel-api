<?php

namespace Tests\TestCases\Unit\Resource\Operations;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\BasicApiResource;
use Tests\Helpers\MocksGuzzle;

class CreateTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::post('basicApiResources.create', '/basicApiResources');
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned
     * - No exceptions are thrown
     */
    public function testCreate()
    {
        $attributes = [
            'id' => 1
        ];
        $data = array_merge($attributes, [
            'name' => 'George Burdell'
        ]);
        $json = json_encode($data);
        $response = BasicApiResource::create($attributes, [], $this->getMockClient(200, $json));

        $this->assertInstanceOf(BasicApiResource::class, $response);
        $this->assertTrue($response->hasAttribute('id'));
        $this->assertEquals($data['id'], $response->id);
        $this->assertEquals($data['name'], $response->name);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (same instance)
     * - No exceptions are thrown
     */
    public function testStore()
    {
        $data = [
            'id' => 1,
            'name' => 'George Burdell'
        ];
        $json = json_encode($data);
        $resource = new BasicApiResource($data);
        $response = $resource->store(client: $this->getMockClient(body: $json));

        $this->assertEquals($response, $response);
        $this->assertEquals($data, $response->attributes);
    }
}
