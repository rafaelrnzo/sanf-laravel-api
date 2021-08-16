<?php


namespace Sanf\Api\Modules\Location;

use NbsPhp\Core\Controllers\RestController;
use Sanf\Api\Modules\Location\Dto\GetListCityDto;
use Sanf\Api\Modules\Location\Dto\GetListDistrictDto;
use Sanf\Api\Modules\Location\Dto\GetListSubDistrictDto;
use Sanf\Api\Modules\Location\Transformers\CityListTransformer;
use Sanf\Api\Modules\Location\Transformers\DistrictListTransformer;
use Sanf\Api\Modules\Location\Transformers\ProvinceListTransformer;
use Sanf\Api\Modules\Location\Transformers\SubDistrictListTransformer;
use Sanf\Core\Modules\Location\GetListCityService;
use Sanf\Core\Modules\Location\GetListDistrictService;
use Sanf\Core\Modules\Location\GetListProvinceService;
use Sanf\Core\Modules\Location\GetListSubDistrictService;
use Sanf\Integration\InternalApiClient;

class LocationController extends RestController
{

    public function provinces(GetListProvinceService $service)
    {
        $result = $service->execute();

        return fractal($result, ProvinceListTransformer::class);
    }

    public function cities(GetListCityService $service,
                           $province_id)
    {
        $dto = new GetListCityDto([
           'province_id' => $province_id,
        ]);
        $result = $service->execute($dto);

        return fractal($result, CityListTransformer::class);
    }

    public function districts(GetListDistrictService $service,
                              $province_id,
                              $city_id)
    {
        $dto = new GetListDistrictDto([
            'province_id' => $province_id,
            'city_id' => $city_id,
        ]);
        $result = $service->execute($dto);

        return fractal($result, DistrictListTransformer::class);
    }

    public function subDistricts(GetListSubDistrictService $service,
                                 $province_id,
                                 $city_id,
                                 $district_name)
    {
        $dto = new GetListSubDistrictDto([
            'province_id' => $province_id,
            'city_id' => $city_id,
            'district_name' => $district_name,
        ]);
        $result = $service->execute($dto);

        return fractal($result, SubDistrictListTransformer::class);
    }
}