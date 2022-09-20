<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Api\Modules\User\Transformers\PostDeactivateAccountTransformer;
use Sanf\Core\Modules\User\Services\PostDeactivateAccountService;

class AuthUserControllerByUser extends RestApiController
{
    public function postDeactivate(
        Guard $auth,
        Request $request,
        PostDeactivateAccountService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $dto = (object)['userId' => $auth->id(),];

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $result = $transactionalService->execute($dto);

        return fractal($result, PostDeactivateAccountTransformer::class)
            ->serializeWith(new ArraySerializer());
    }
}
