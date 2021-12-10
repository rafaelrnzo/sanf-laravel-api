<?php

namespace Sanf\Api\Modules\InvoiceCollection\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\InvoiceCollection\Services\BrowseFinancingUnitByUserService;

final class FinancingUnitByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseFinancingUnitByUserService $service)
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
        "contract_no": "2012321232323",
        "serial_no": "ZX12387SJKSD",
        "provider_name": "Shimizu",
        "brand_name": "Excavator",
        "type_name": "Buldozer",
        "model_name": "0912323232",
        "year": "2021"
      }
    ],
    "metadata": {
      "count": 0,
      "skip": 0,
      "limit": 10,
      "sort_by": "earliest"
    }
  }', true);
//        $dto = new BrowseFinancingUnitByUserRequestDto($input + ['userId' => $auth->id()]);
//        $result = $service->execute($dto);
//
//        return fractal($result->data, new FinancingUnitSimpleTransformer())
//            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
