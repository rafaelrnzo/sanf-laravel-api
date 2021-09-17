<?php

namespace Sanf\Api\Modules\User\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\PositionTransformer;
use Sanf\Core\Modules\User\GetListPositionService;
use Spatie\Fractalistic\ArraySerializer;

class PositionController extends RestApiController
{
    public function getList(GetListPositionService $service)
    {
        $response = $service->execute();

        $data = fractal($response, PositionTransformer::class)->serializeWith(new ArraySerializer());
        return collect($data)->flatten()->all();
    }
}
