<?php

namespace Sanf\Api\Modules\Scanina\Controllers\Product;

use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\ReadProductServiceResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GuzzleReadProductServicesService;

class ReadProductServiceController extends RestApiController
{
    public function __invoke(string $xid, Guard $userAuth, GuzzleReadProductServicesService $service)
    {
        $dto = (object) [
            'xid' => $xid,
            'userId' => $userAuth->id(),
        ];
        $result = $service->execute($dto);

        return fractal($result, ReadProductServiceResponseTransformer::class);
    }
}
