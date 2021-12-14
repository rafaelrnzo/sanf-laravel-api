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
        return json_decode('{
    "contract_no": "30712000741",
    "prepayment_date": "2021-12-14",
    "total_prepayment": "1475000000",
    "items": [
      {
        "description": "Outstanding Principal",
        "amount": "1302649294.02"
      },
      {
        "description": "Installment Overdue",
        "amount": "107974000"
      },
      {
        "description": "Prepayment Penalty",
        "amount": "32566232"
      },
      {
        "description": "Advance Payment Customer",
        "amount": "0"
      },
      {
        "description": "Admin Charge Prepay",
        "amount": "500000"
      },
      {
        "description": "Overdue Penalty",
        "amount": "15856557.98"
      },
      {
        "description": "Bunga Berjalan Prepay",
        "amount": "15453916"
      }
    ]
  }',true);
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
//        return $this->responseOk();
    }
}
