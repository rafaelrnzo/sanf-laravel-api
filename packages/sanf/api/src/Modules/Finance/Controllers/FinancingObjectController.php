<?php

namespace Sanf\Api\Modules\Finance\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Finance\Transformers\BrandListTransformer;
use Sanf\Api\Modules\Finance\Transformers\ModelListTransformer;
use Sanf\Api\Modules\Finance\Transformers\TypeListTransformer;
use Sanf\Core\Modules\Finance\Services\GetBrandListService;
use Sanf\Core\Modules\Finance\Services\GetModelListService;
use Sanf\Core\Modules\Finance\Services\GetTypeListService;

class FinancingObjectController extends RestApiController
{
    public function getBrands(GetBrandListService $service)
    {
        $result = $service->execute();

        return fractal($result->data, BrandListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getTypes($brand_id, GetTypeListService $service)
    {
        $dto = (object)[
            'brand_id' => $brand_id
        ];

        $result = $service->execute($dto);

        return fractal($result->data, TypeListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getModels($brand_id, $type_id, GetModelListService $service)
    {
        $dto = (object)[
            'brand_id' => $brand_id,
            'type_id' => $type_id,
        ];
        $result = $service->execute($dto);

        return fractal($result->data, ModelListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}