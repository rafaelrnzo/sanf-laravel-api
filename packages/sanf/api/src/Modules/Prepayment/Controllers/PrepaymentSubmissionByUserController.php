<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Prepayment\Services\AddPrepaymentSubmissionByUserService;
use Sanf\Core\Modules\Prepayment\Services\BrowsePrepaymentSubmissionByUserService;
use Sanf\Core\Modules\Prepayment\Services\DeletePrepaymentSubmissionByUserService;
use Sanf\Core\Modules\Prepayment\Services\EditPrepaymentSubmissionByUserService;
use Sanf\Core\Modules\Prepayment\Services\PatchPrepaymentSubmissionByUserService;
use Sanf\Core\Modules\Prepayment\Services\ReadPrepaymentSubmissionByUserService;

final class PrepaymentSubmissionByUserController extends RestApiController
{
    public function postAdd(Guard $auth, Request $request, AddPrepaymentSubmissionByUserService $service)
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
//        $dto = new AddPrepaymentSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
//        $service->execute($dto);
        return $this->responseOk();
    }
}
