<?php

namespace Sanf\External\Modules\Customer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Dashboard\Modules\User\UseCases\CreateCustomerFromCoreUseCase;

class CustomerController extends RestApiController
{
    public function postAdd(
        Request $request,
        CreateCustomerFromCoreUseCase $createUseCase
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

        DB::connection('dashboard_db')->transaction(function () use ($formRequest, $createUseCase) {
            $result = $createUseCase->execute($formRequest);
        });

        return $this->responseOk();
    }
}
