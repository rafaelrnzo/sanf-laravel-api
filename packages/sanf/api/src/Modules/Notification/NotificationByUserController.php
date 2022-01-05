<?php

namespace Sanf\Api\Modules\Notification;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use NbsPhp\Notification\Dtos\BrowseNotificationByUserRequestDto;
use NbsPhp\Notification\Services\BrowseNotificationByUserService;

final class NotificationByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, BrowseNotificationByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);

//        return json_decode('{
//    "rows": [
//      {
//        "xid": "12JSD23L",
//        "title": "Pengajuan Pembiayaan Disetujui",
//        "subtitle": "Daniel Ricardo",
//        "body": "Pengajuan dengan nomor 07.......",
//        "type": 1,
//        "screen": "financing_application_approved",
//        "published_at": 1641282071,
//        "created_at": 1641282071
//      },
//      {
//        "xid": "KJSDA2",
//        "title": "Tagihan sudah jatuh tempo",
//        "subtitle": "PT Sukses Maju Sejahtera",
//        "body": "Angsuran dengan nomor 01",
//        "type": 2,
//        "screen": "contract_detail|ASDK1232",
//        "published_at": 1641282071,
//        "created_at": 1641282071
//      },
//      {
//        "xid": "XCMNSADKI23431",
//        "title": "Annual Report SANF 2021",
//        "subtitle": "Admin SANF",
//        "body": "Lorem ipsum dolor sit amet",
//        "type": 1,
//        "screen": "browser|https://sanf.co.id",
//        "published_at": 1641282071,
//        "created_at": 1641282071
//      },
//      {
//        "xid": "PPREIU2329878891",
//        "title": "Promo Spesial Pembiayaan Alat Berat",
//        "subtitle": "Promo & News",
//        "body": "Lorem ipsum dolor sit amet",
//        "type": 1,
//        "screen": "webview|https://sanf.co.id",
//        "published_at": 1641282071,
//        "created_at": 1641282071
//      }
//    ],
//    "metadata": {
//      "count": 4,
//      "skip": 0,
//      "limit": 10,
//      "sort_by": "earliest"
//    }
//  }',true);
        $dto = new BrowseNotificationByUserRequestDto($input + ['userId' => $auth->id()]);
        $result = $service->execute($dto);

        return fractal($result->data, new MyNotificationSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
