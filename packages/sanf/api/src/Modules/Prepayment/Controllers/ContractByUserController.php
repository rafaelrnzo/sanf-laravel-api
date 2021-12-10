<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Prepayment\Services\BrowseContractByUserService;

final class ContractByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseContractByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        return json_decode('{
    "rows": [
      {
        "contract_no": "2IK12300232",
        "is_submitted": true,
        "remaining_balance": "120000",
        "currency_type": "IDR"
      }
    ],
    "metadata": {
      "count": 1,
      "skip": 0,
      "limit": 10,
      "sort_by": "earliest"
    }
  }', true);
//        $dto = new BrowseContractByUserRequestDto($input + ['userId' => $auth->id()]);
//        $result = $service->execute($dto);
//
//        return fractal($result->data, new ContractSimpleTransformer())
//            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
