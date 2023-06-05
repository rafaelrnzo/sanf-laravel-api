<?php

namespace Sanf\Api\Modules\Scanina\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\ReadBannerResponseTransformer;
use Sanf\Core\Modules\Scanina\Services\GetScaninaBannerService;

class ReadBannerController extends RestApiController
{
    public function __invoke(GetScaninaBannerService $service)
    {
        $result = $service->execute();

        return fractal($result, ReadBannerResponseTransformer::class);
    }
}
