<?php


namespace Sanf\Api\Modules\Staff;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\MockLazyPaginatorAdapter;

class StaffController extends RestApiController
{
    public function getList(Request $request)
    {
        $data = json_decode(json_encode([
            [
                "no" => "1",
                "name" => "staff 1",
                "email" => "staff1@sanf.co.id",
                "statusId" => 10,
                "statusName" => "Aktif"
            ],
            [
                "no" => "2",
                "name" => "staff 1",
                "email" => "staff1@sanf.co.id",
                "statusId" => 30,
                "statusName" => "Belum Aktivasi"
            ]
        ]));

        return fractal($data, new StaffTransformer())->paginateWith(new MockLazyPaginatorAdapter($data));
    }

    public function getInvitedList(Request $request)
    {
        $data = json_decode(json_encode([
            [
                "no" => "1",
                "name" => "staff 1",
                "email" => "staff1@sanf.co.id",
                "statusId" => 10,
                "statusName" => "Aktif"
            ],
            [
                "no" => "2",
                "name" => "staff 1",
                "email" => "staff1@sanf.co.id",
                "statusId" => 30,
                "statusName" => "Belum Aktivasi"
            ]
        ]));
        return fractal($data, new StaffTransformer())->paginateWith(new MockLazyPaginatorAdapter($data));
    }

    public function postActivate(Request $request)
    {
        return $this->responseOk();
    }

    public function postDeactivate(Request $request)
    {
        return $this->responseOk();
    }
}
