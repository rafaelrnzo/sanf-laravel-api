<?php

namespace Sanf\Api\Modules\Scanina\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Scanina\Transformers\ReadBannerResponseTransformer;
use Sanf\Api\Modules\Scanina\Transformers\ReadProductSparePartResponseTransformer;

class ReadBannerController extends RestApiController
{
    public function __invoke()
    {
        $dto = (object)[
            'directory' => 'temp/',
            'filename' => 'Uz7Efb1BJo4fuRPt3HTDu77RMaPC3EtWfyoPn312.png',
            'path' => 'temp/Uz7Efb1BJo4fuRPt3HTDu77RMaPC3EtWfyoPn312.png',
        ];

        return fractal($dto, ReadBannerResponseTransformer::class);
    }
}
