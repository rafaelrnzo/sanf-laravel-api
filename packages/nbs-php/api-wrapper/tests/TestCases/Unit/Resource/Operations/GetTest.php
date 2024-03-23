<?php

namespace Tests\TestCases\Unit\Resource\Operations;

use NbsPhp\ApiWrapper\Api\Route;
use PHPUnit\Framework\TestCase;
use Tests\Assets\Resource\Resources\BasicApiResource;
use Tests\Helpers\MocksGuzzle;

class GetTest extends TestCase
{
    use MocksGuzzle;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Route::get('basicApiResources.get', '/basicApiResources/{id}');
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned.
     */
    public function testGet()
    {
        $data = [
            'id' => 1,
        ];
        $json = json_encode($data);
        // $response = BasicApiResource::get(id: 1, client: $this->getMockClient(body: $json));
        //
        // $this->assertInstanceOf(BasicApiResource::class, $response);
        // $this->assertEquals($data, $response->attributes);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (same instance).
     */
    public function testRefresh()
    {
        $data = [
            'id' => 1,
        ];
        $json = json_encode($data);
        $resource = new BasicApiResource([
            'id' => 2,
            'name' => 'George Burdell',
        ]);
        $response = $resource->refresh([], $this->getMockClient(200, $json));

        $this->assertInstanceOf(BasicApiResource::class, $response);
        $this->assertEquals($resource, $response);
        $this->assertEquals($data, $response->attributes);
    }

    /**
     * Expected Behavior:
     * - ApiResource instance with correct attributes returned (new instance).
     */
    public function testFresh()
    {
        $data = [
            'id' => 1,
        ];
        $json = json_encode($data);
        $resource = new BasicApiResource([
            'id' => 2,
            'name' => 'George Burdell',
        ]);
        $response = $resource->fresh([], $this->getMockClient(200, $json));

        $this->assertInstanceOf(BasicApiResource::class, $response);
        $this->assertNotEquals($resource, $response);
        $this->assertEquals($data, $response->attributes);
    }
}
