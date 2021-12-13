<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Prepayment\Services\AddPrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\BrowsePrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\DeletePrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\EditPrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\PatchPrepaymentSimulationByUserService;
use Sanf\Core\Modules\Prepayment\Services\ReadPrepaymentSimulationByUserService;

final class PrepaymentSimulationByUserController extends RestApiController
{
    public function postAdd(Guard $auth, Request $request, AddPrepaymentSimulationByUserService $service)
    {
//        $input = $this->validate($request, [
//            'email' => ['required', 'email', 'max:255'],
//            'title' => ['required', 'string', 'max:255'],
//            'description' => ['nullable', 'string', 'max:65535'],
//            'total' => ['nullable', 'integer', 'max:2147483647'],
//            'price' => ['nullable', 'numeric', 'max:999999999999999.9999'],
//            'is_enabled' => ['nullable', 'boolean'],
//            'images' => ['nullable', 'array'],
//            'created_at' => ['nullable', 'integer', 'max:99999999999']
//        ]);
//        $dto = new AddPrepaymentSimulationByUserRequestDto($input + ['userId' => $auth->id()]);
//        $service->execute($dto);
        return $this->responseOk();
    }
}
