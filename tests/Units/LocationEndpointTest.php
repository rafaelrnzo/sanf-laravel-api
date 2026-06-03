<?php

namespace Tests\Units;

use Illuminate\Support\Facades\Http;
use TestCase;

class LocationEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google_maps.api_key' => 'dummy_key']);
    }

    public function testReverseGeocodeSuccess()
    {
        $this->withoutMiddleware();

        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'results' => [
                    [
                        'formatted_address' => 'Jl. Merdeka No. 1, Gambir, Jakarta Pusat, DKI Jakarta, Indonesia',
                    ]
                ]
            ], 200)
        ]);

        $payload = [
            'Latitude' => -6.214612,
            'Longitude' => 106.845172,
        ];

        $this->post('/v1/users/reverse-geocode', $payload);

        $this->seeStatusCode(200);
        $this->seeJsonEquals([
            'success' => true,
            'code' => '200',
            'message' => 'OK',
            'data' => [
                'address' => 'Jl. Merdeka No. 1, Gambir, Jakarta Pusat, DKI Jakarta, Indonesia'
            ]
        ]);
    }

    public function testReverseGeocodeValidationFailed()
    {
        $this->withoutMiddleware();

        // Missing parameters
        $this->post('/v1/users/reverse-geocode', []);
        $this->seeStatusCode(422);

        // Invalid Latitude
        $this->post('/v1/users/reverse-geocode', [
            'Latitude' => 'not-a-number',
            'Longitude' => 106.845172,
        ]);
        $this->seeStatusCode(422);
    }

    public function testReverseGeocodeMissingApiKey()
    {
        $this->withoutMiddleware();
        config(['services.google_maps.api_key' => null]);

        $payload = [
            'Latitude' => -6.214612,
            'Longitude' => 106.845172,
        ];

        $this->post('/v1/users/reverse-geocode', $payload);

        $this->seeStatusCode(200);
        $this->seeJsonEquals([
            'success' => true,
            'code' => '200',
            'message' => 'No address found',
            'data' => [
                'address' => null,
            ]
        ]);
    }

    public function testReverseGeocodeGoogleMapsZeroResults()
    {
        $this->withoutMiddleware();

        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'ZERO_RESULTS',
                'results' => []
            ], 200)
        ]);

        $payload = [
            'Latitude' => -6.214612,
            'Longitude' => 106.845172,
        ];

        $this->post('/v1/users/reverse-geocode', $payload);

        $this->seeStatusCode(200);
        $this->seeJsonEquals([
            'success' => true,
            'code' => '200',
            'message' => 'No address found',
            'data' => [
                'address' => null,
            ]
        ]);
    }

    public function testReverseGeocodeGoogleMapsErrorStatus()
    {
        $this->withoutMiddleware();

        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'REQUEST_DENIED',
                'error_message' => 'The provided API key is invalid.'
            ], 200)
        ]);

        $payload = [
            'Latitude' => -6.214612,
            'Longitude' => 106.845172,
        ];

        $this->post('/v1/users/reverse-geocode', $payload);

        $this->seeStatusCode(200);
        $this->seeJsonEquals([
            'success' => true,
            'code' => '200',
            'message' => 'No address found',
            'data' => [
                'address' => null,
            ]
        ]);
    }
}
