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
use Spatie\Fractalistic\ArraySerializer;

class CoreLocationController extends RestApiController
{

    public function provinces(GetListProvinceService $service)
    {
        $result = $service->execute();

        return fractal($result, ProvinceListTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function cities(GetListCityService $service,
                                              $province_id)
    {
        $dto = new GetListCityDto([
            'province_id' => $province_id,
        ]);
        $result = $service->execute($dto);

        return fractal($result, CityListTransformer::class)->serializeWith(new ArraySerializer());
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

        return fractal($result, DistrictListTransformer::class)->serializeWith(new ArraySerializer());
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

        return fractal($result, SubDistrictListTransformer::class)->serializeWith(new ArraySerializer());
    }

    public function getAllCities(Request $request)
    {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'string', 'max:32'],
        ]);

        $dto = new ListCityDto($input);

        $result = (object)[
            'data' => [
                (object)['id' => '001001', 'name' => 'Ambon'],
                (object)['id' => '001002', 'name' => 'Balikpapan'],
                (object)['id' => '001003', 'name' => 'Banda Aceh'],
                (object)['id' => '001004', 'name' => 'Bandar Lampung'],
                (object)['id' => '001005', 'name' => 'Bandung'],
                (object)['id' => '001006', 'name' => 'Banjar'],
                (object)['id' => '001007', 'name' => 'Banjarbaru'],
                (object)['id' => '001008', 'name' => 'Banjarmasin'],
                (object)['id' => '001009', 'name' => 'Banjar'],
                (object)['id' => '001010', 'name' => 'Batu'],
                (object)['id' => '001011', 'name' => 'Baubau'],
            ],
            'paginate' => (object)[
                'total' => 1,
                'count' => 1,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];

        return fractal($result->data, AllCityListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
