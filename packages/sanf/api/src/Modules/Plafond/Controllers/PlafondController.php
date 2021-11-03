<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Plafond\Transformers\PlafondTypeListTransformer;
use Sanf\Core\Modules\Plafond\Services\ListPlafondTypeService;

class PlafondController extends RestApiController
{

    public function getTypes(ListPlafondTypeService $service)
    {
        // TODO refactor this static pagination filter
        $dto = (object)[
            'limit' => 10,
            'skip' => 0,
            'sort_by' => 'default',
        ];
        $result = $service->execute($dto);

        return fractal($result->data, PlafondTypeListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}