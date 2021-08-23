<?php

namespace Sanf\Api\Modules\User;

use NbsPhp\Core\Controllers\RestController;
use Sanf\Api\Modules\User\Transformers\PositionTransformer;
use Sanf\Core\Modules\User\GetListPositionService;

class PositionController extends RestController
{
    public function getList(GetListPositionService $service)
    {
        $response = $service->execute();

        return fractal($response, PositionTransformer::class);
    }
}
