<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\TitleTransformer;
use Sanf\Core\Modules\User\Dto\GetListTitleDto;
use Sanf\Core\Modules\User\Services\GetListTitleService;
use Spatie\Fractalistic\ArraySerializer;

class TitleController extends RestApiController
{
    public function getList(GetListTitleService $service, Request $request)
    {
        $dto = new GetListTitleDto([
            'type' => strtoupper($request->type),
        ]);

        $response = $service->execute($dto);

        return fractal(json_decode(json_encode($response)), TitleTransformer::class)->serializeWith(new ArraySerializer());
    }
}
