<?php

namespace Sanf\Api\Modules\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Location\Transformers\LocationListTransformer;
use Sanf\Core\Modules\Location\GetListLocationDto;
use Sanf\Core\Modules\Location\GetListLocationService;
use Sanf\Core\Modules\Location\LocationEnum;

class LocationController extends RestApiController
{
    public function getList(Request $request, GetListLocationService $service)
    {
        $this->validate($request, [
            'level' => ['required', 'integer', Rule::in([
                LocationEnum::PROVINCE_LV,
                LocationEnum::CITY_LV,
                LocationEnum::DISTRICT_LV,
                LocationEnum::SUBDISTRICT_LV,
            ])],
            'xid' => 'nullable|string',
            'keyword' => 'nullable|string',
            'skip' => 'nullable|integer',
            'limit' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['earliest', 'latest', 'name_desc', 'name_asc'])],
        ]);

        $dto = new GetListLocationDto([
            'level' => $request->input('level'),
            'xid' => $request->input('xid'),
            'keyword' => $request->input('keyword'),
            'skip' => $request->input('skip'),
            'limit' => $request->input('limit'),
            'sort_by' => $request->input('sort_by') ?? 'earliest',
        ]);

        $result = $service->execute($dto);

        return fractal($result->data, LocationListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function reverseGeocode(Request $request)
    {
        $this->validate($request, [
            'Latitude' => ['required', 'numeric', 'between:-90,90'],
            'Longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $lat = $request->input('Latitude');
        $lng = $request->input('Longitude');

        $apiKey = config('services.google_maps.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => true,
                'code' => '200',
                'message' => 'No address found',
                'data' => [
                    'address' => null,
                ],
            ]);
        }

        try {
            $response = Http::timeout(5)->get("https://maps.googleapis.com/maps/api/geocode/json", [
                'latlng' => $lat . ',' . $lng,
                'key' => $apiKey
            ]);
            
            if ($response->successful() && $response->json('status') === 'OK') {
                $address = $response->json('results.0.formatted_address');
                return response()->json([
                    'success' => true,
                    'code' => '200',
                    'message' => 'OK',
                    'data' => [
                        'address' => $address,
                    ],
                ]);
            }
            
            return response()->json([
                'success' => true,
                'code' => '200',
                'message' => 'No address found',
                'data' => [
                    'address' => null,
                ],
            ]);
            
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => true,
                'code' => '200',
                'message' => 'No address found',
                'data' => [
                    'address' => null,
                ],
            ]);
        }
    }
}
