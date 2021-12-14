<?php

namespace Sanf\Api\Modules\Insurance\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Insurance\Dtos\ReadInsuranceClaimSubmissionByUserRequestDto;
use Sanf\Core\Modules\Insurance\Services\AddInsuranceClaimSubmissionByUserService;
use Sanf\Core\Modules\Insurance\Services\BrowseInsuranceClaimSubmissionByUserService;
use Sanf\Core\Modules\Insurance\Services\ReadInsuranceClaimSubmissionByUserService;

final class InsuranceClaimSubmissionByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseInsuranceClaimSubmissionByUserService $service)
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
        "xid": "lklasd123876123",
        "serial_no": "J21232",
        "polis_no": "02052012232",
        "brand_type_model": "KOMATSU HYDRAULIC EXCAVATOR PC130F-7/P7",
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
//        $dto = new BrowseInsuranceClaimSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
//        $result = $service->execute($dto);
//
//        return fractal($result->data, new InsuranceClaimSubmissionSimpleTransformer())
//            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getRead(Guard $auth, $xid, ReadInsuranceClaimSubmissionByUserService $service)
    {
        $dto = new ReadInsuranceClaimSubmissionByUserRequestDto([
            'xid' => $xid,
            'userId' => $auth->id()
        ]);
        return json_decode('{
    "xid": "lklasd123876123",
    "serial_no": "J21232",
    "polis_no": "02052012232",
    "brand_type_model": "KOMATSU HYDRAULIC EXCAVATOR PC130F-7/P7",
    "status": {
      "id": 10,
      "name": "Diproses"
    },
    "location_metadata": {
      "city_id": "10010010",
      "city_name": "Jakarta Utara"
    },
    "incident_date": "2021-12-01",
    "description": "ini deskripsi",
    "image_files": [
      {
        "file_name": "JSK923123s132.png",
        "origin_name": "fff.png",
        "url": "https://via.placeholder.com/300/09f/fff.png"
      }
    ]
  }', true);
//        $result = $service->execute($dto);
//        return fractal($result, new InsuranceClaimSubmissionTransformer());
    }

    public function postAdd(Guard $auth, Request $request, AddInsuranceClaimSubmissionByUserService $service)
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
//        $dto = new AddInsuranceClaimSubmissionByUserRequestDto($input + ['userId' => $auth->id()]);
//        $service->execute($dto);
        return $this->responseOk();
    }
}
