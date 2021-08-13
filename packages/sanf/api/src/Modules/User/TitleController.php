<?php


namespace Sanf\Api\Modules\User;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Api\Modules\User\Dto\GetListTitleDto;
use Sanf\Core\Modules\User\GetListTitleService;

class TitleController extends RestController
{

    public function getList(GetListTitleService $service, Request $request)
    {
        $dto = new GetListTitleDto([
            'type' => strtoupper($request->type),
        ]);

        $response = $service->execute($dto);

        return fractal(json_decode(json_encode($response)), TitleTransformer::class);
    }
}
