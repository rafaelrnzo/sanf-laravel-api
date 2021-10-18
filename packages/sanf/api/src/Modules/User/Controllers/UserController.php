<?php


namespace Sanf\Api\Modules\User\Controllers;


use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\User\Transformers\PersonalAssistantTransformer;
use Sanf\Api\Modules\User\Transformers\UserMetadataInfoTransformer;
use Sanf\Core\Modules\Commodity\Services\GetCommodityMetadataByUserService;
use Sanf\Core\Modules\Project\Services\GetProjectMetadataByUserService;
use Sanf\Core\Modules\User\Services\GetPersonalAssistantUserService;

class UserController extends RestApiController
{
    public function getProjectMetadataInfo(
        Guard $auth,
        GetProjectMetadataByUserService $projectService,
        GetCommodityMetadataByUserService $commodityService
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

    public function getPersonalAssistant(
        Guard $auth,
        GetPersonalAssistantUserService $service
    ) {
        $dto = (object)['userId' => $auth->id()];
        $result = $service->execute($dto);
        return fractal($result, new PersonalAssistantTransformer());
    }
}
