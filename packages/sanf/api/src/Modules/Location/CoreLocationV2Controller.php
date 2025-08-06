<?php

namespace Sanf\Api\Modules\Location;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Location\Transformers\AllCityListTransformer;
use Sanf\Core\Modules\Location\ListCityV2Dto;
use Sanf\Core\Modules\Location\Services\AllCityListV2Service;

/**
 * @since CR2025
 */
class CoreLocationV2Controller extends RestApiController
{
    public function getCities(Request $request, AllCityListV2Service $service)
    {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:name_asc,name_desc'],
        ]);

        $dto = new ListCityV2Dto($input);

        $result = $service->execute($dto);

        return fractal($result->data, AllCityListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
