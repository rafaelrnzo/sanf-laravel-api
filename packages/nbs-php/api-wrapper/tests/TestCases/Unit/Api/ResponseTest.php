<?php

namespace Tests\TestCases\Unit\Api;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use NbsPhp\ApiWrapper\Api\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testGetContents()
    {
        $data = [
            'abc' => 123,
        ];

        $json = json_encode($data);

        $response = new Response(new GuzzleResponse(
            200,
            [],
            $json
        ));

        $this->assertEquals($json, $response->getContents());
    }

    public function testJson()
    {
        $data = [
            'abc' => 123,
        ];

        $json = json_encode($data);

        $response = new Response(new GuzzleResponse(
            200,
            [],
            $json
        ));

        $this->assertEquals($data, $response->json());
        $this->assertEquals((object) $data, $response->json(false));
    }
}
