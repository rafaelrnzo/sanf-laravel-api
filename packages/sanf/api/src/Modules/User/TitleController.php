<?php


namespace Sanf\Api\Modules\User;


use NbsPhp\Core\Controllers\RestController;
use Sanf\Api\Modules\User\Dto\GetListTitleDto;
use Sanf\Core\Modules\User\GetListTitleService;

class TitleController extends RestController
{

    public function getList(GetListTitleService $service, $type)
    {
        $dto = new GetListTitleDto([
            'type' => strtoupper($type),
        ]);

        $response = $service->execute($dto);

        return fractal(json_decode(json_encode($response)), TitleTransformer::class);
    }
}
