<?php

namespace Sanf\Core\Modules\Location\Services;

use Illuminate\Support\Facades\Http;

class ReverseGeocodeService
{
    public function execute(float $latitude, float $longitude): ?string
    {
        $apiKey = config('services.google_maps.api_key');

        if (empty($apiKey)) {
            return null;
        }

        $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => $latitude . ',' . $longitude,
            'key' => $apiKey,
        ]);

        if (!$response->successful() || $response->json('status') !== 'OK') {
            return null;
        }

        $address = $response->json('results.0.formatted_address');

        return is_string($address) && $address !== '' ? $address : null;
    }
}
