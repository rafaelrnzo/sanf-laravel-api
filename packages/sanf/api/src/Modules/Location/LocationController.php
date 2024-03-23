<?php

namespace Sanf\Api\Modules\Location;

use Illuminate\Http\Request;
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
}
