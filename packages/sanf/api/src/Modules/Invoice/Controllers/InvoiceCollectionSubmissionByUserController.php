<?php

namespace Sanf\Api\Modules\Invoice\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Invoice\Services\AddInvoiceCollectionSubmissionByUserService;
use Sanf\Core\Modules\Invoice\Services\BrowseInvoiceCollectionSubmissionByUserService;
use Sanf\Core\Modules\Invoice\Services\DeleteInvoiceCollectionSubmissionByUserService;
use Sanf\Core\Modules\Invoice\Services\EditInvoiceCollectionSubmissionByUserService;
use Sanf\Core\Modules\Invoice\Services\PatchInvoiceCollectionSubmissionByUserService;
use Sanf\Core\Modules\Invoice\Services\ReadInvoiceCollectionSubmissionByUserService;

final class InvoiceCollectionSubmissionByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseInvoiceCollectionSubmissionByUserService $service)
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
        "contract_no": "1209234232",
        "serial_no": "KXXD220023",
        "pickup_date": "2021-12-20",
        "brand_type_model": "KOMATSU HYDRAULIC EXCAVATOR PC130F-7/P7",
        "year": "2021",
        "status": {
          "id": 10,
          "name": "Diproses"
        }
      }
    ],
    "metadata": {
      "count": 1,
      "skip": 0,
      "limit": 10,
      "sort_by": "earliest"
    }
  }', true);
//        $dto = new BrowseInvoiceCollectionSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
//        $result = $service->execute($dto);
//
//        return fractal($result->data, new InvoiceCollectionSubmissionSimpleTransformer())
//            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postAdd(Guard $auth, Request $request, $xid, AddInvoiceCollectionSubmissionByUserService $service)
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
//        $dto = new AddInvoiceCollectionSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
//        $service->execute($dto);
        return $this->responseOk();
    }
}
