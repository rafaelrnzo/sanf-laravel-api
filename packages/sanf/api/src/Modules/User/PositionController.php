<?php

namespace Sanf\Api\Modules\User;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\PositionTransformer;
use Sanf\Core\Modules\User\GetListPositionService;

class PositionController extends RestApiController
{
    public function getList(GetListPositionService $service)
    {
        $response = $service->execute();

        $data = fractal($response, PositionTransformer::class);
        return collect($data)->flatten()->all();
    }
}
