<?php


namespace Sanf\Api\Modules\User;


use Illuminate\Contracts\Auth\Guard;
use Sanf\Api\Modules\User\Transformers\UserMetadataInfoTransformer;
use Sanf\Core\Modules\Commodity\Services\GetUserCommodityMetadataService;
use Sanf\Core\Modules\Project\Services\GetUserProjectMetadataService;

class UserController
{
    public function getProjectMetadataInfo(
        Guard $auth,
        GetUserProjectMetadataService $projectService,
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
