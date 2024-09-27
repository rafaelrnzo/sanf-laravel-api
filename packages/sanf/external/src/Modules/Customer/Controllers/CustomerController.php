<?php

namespace Sanf\External\Modules\Customer\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Database\IlluminateFullMultipleSession;
use Sanf\Core\Services\MultipleTransactionalApplicationService;
use Sanf\Dashboard\Modules\User\UseCases\CreateCustomerFromCoreUseCase;

class CustomerController extends RestApiController
{
    public function postAdd(
        Request $request,
        CreateCustomerFromCoreUseCase $createUseCase,
        IlluminateFullMultipleSession $transactionalSession
    ) {
        $this->validate($request, [
            'bowheer.id' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bowheer.name' => ['required', 'string', 'max:255', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bowheer.email' => ['required', 'email', 'max:255', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bowheer.code' => ['required', 'string', 'max:255', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
        ]);

        $formRequest = (object) [
            'id' => $request->get('bowheer')['id'],
            'name' => $request->get('bowheer')['name'],
            'email' => $request->get('bowheer')['email'],
            'code' => $request->get('bowheer')['code'],
        ];

        $transactionalService = new MultipleTransactionalApplicationService($createUseCase, $transactionalSession);
        $result = $transactionalService->execute($formRequest);

        return $this->responseOk();
    }
}
