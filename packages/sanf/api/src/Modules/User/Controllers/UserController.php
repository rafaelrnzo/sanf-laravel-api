<?php


namespace Sanf\Api\Modules\User\Controllers;


use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\UserMetadataInfoTransformer;
use Sanf\Core\Modules\Commodity\Services\GetUserCommodityMetadataService;
use Sanf\Core\Modules\Project\Services\GetProjectMetadataByUserService;

class UserController extends RestApiController
{
    public function getProjectMetadataInfo(
        Guard $auth,
        GetProjectMetadataByUserService $projectService,
        GetUserCommodityMetadataService $commodityService
    ) {
        $dto = (object)['userId' => $auth->id()];
        $projectMetadata = $projectService->execute($dto);
        $commodityMetadata = $commodityService->execute($dto);
        return fractal((object)[
            'projectMetadata' => $projectMetadata,
            'commodityMetadata' => $commodityMetadata,
        ],
            new UserMetadataInfoTransformer()
        );
    }
}
