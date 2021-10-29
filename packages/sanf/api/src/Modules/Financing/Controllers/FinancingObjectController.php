<?php

namespace Sanf\Api\Modules\Financing\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\BrandListTransformer;
use Sanf\Api\Modules\Financing\Transformers\ModelListTransformer;
use Sanf\Api\Modules\Financing\Transformers\TypeListTransformer;
use Sanf\Core\Modules\Financing\Services\GetBrandListService;
use Sanf\Core\Modules\Financing\Services\GetModelListService;
use Sanf\Core\Modules\Financing\Services\GetTypeListService;

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