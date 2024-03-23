<?php

namespace Sanf\Api\Modules\Location;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Location\Dto\GetListCityDto;
use Sanf\Api\Modules\Location\Dto\GetListDistrictDto;
use Sanf\Api\Modules\Location\Dto\GetListSubDistrictDto;
use Sanf\Api\Modules\Location\Transformers\AllCityListTransformer;
use Sanf\Api\Modules\Location\Transformers\CityListTransformer;
use Sanf\Api\Modules\Location\Transformers\DistrictListTransformer;
use Sanf\Api\Modules\Location\Transformers\ProvinceListTransformer;
use Sanf\Api\Modules\Location\Transformers\SubDistrictListTransformer;
use Sanf\Core\Modules\Location\Core\GetListCityService;
use Sanf\Core\Modules\Location\Core\GetListDistrictService;
use Sanf\Core\Modules\Location\Core\GetListProvinceService;
use Sanf\Core\Modules\Location\Core\GetListSubDistrictService;
use Sanf\Core\Modules\Location\ListCityDto;
use Sanf\Core\Modules\Location\Services\AllCityListServices;
use Spatie\Fractalistic\ArraySerializer;

class CoreLocationController extends RestApiController
{
    public function provinces(GetListProvinceService $service)
    {
        $result = $service->execute();

        return fractal($result, ProvinceListTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function cities(
        GetListCityService $service,
        $province_id
    )
    {
        $dto = new GetListCityDto([
            'province_id' => $province_id,
        ]);
        $result = $service->execute($dto);

        return fractal($result, CityListTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function districts(
        GetListDistrictService $service,
        $province_id,
        $city_id
    )
    {
        $dto = new GetListDistrictDto([
            'province_id' => $province_id,
            'city_id' => $city_id,
        ]);
        $result = $service->execute($dto);

        return fractal($result, DistrictListTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function subDistricts(
        GetListSubDistrictService $service,
        $province_id,
        $city_id,
        $district_name
    )
    {
        $dto = new GetListSubDistrictDto([
            'province_id' => $province_id,
            'city_id' => $city_id,
            'district_name' => $district_name,
        ]);
        $result = $service->execute($dto);

        return fractal($result, SubDistrictListTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function getCities(Request $request, AllCityListServices $services)
    {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:name_asc,name_desc'],
        ]);

        $dto = new ListCityDto($input);

        $result = $services->execute($dto);

        return fractal($result->data, AllCityListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
