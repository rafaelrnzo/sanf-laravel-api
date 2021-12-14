<?php

namespace Sanf\Api\Modules\Insurance\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Insurance\Services\BrowseFinancingUnitByUserService;

final class FinancingUnitByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseFinancingUnitByUserService $service)
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
        "brand_type_model": "KOMATSU HYDRAULIC EXCAVATOR PC130F-7/P7",
        "year": "2021"
      }
    ],
    "metadata": {
      "count": 1,
      "skip": 0,
      "limit": 10,
      "sort_by": "earliest"
    }
  }',true);
//        $dto = new BrowseFinancingUnitByUserRequestDto($input + ['userId' => $auth->id()]);
//        $result = $service->execute($dto);
//
//        return fractal($result->data, new FinancingUnitSimpleTransformer())
//            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
