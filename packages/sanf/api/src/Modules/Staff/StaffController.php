<?php


namespace Sanf\Api\Modules\Staff;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;

class StaffController extends RestApiController
{
    public function getList(Request $request)
    {
        $result = json_decode(json_encode([
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
        return fractal($result, new StaffTransformer());
    }

    public function getInvitedList(Request $request)
    {
        $result = json_decode(json_encode([
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
        return fractal($result, new StaffTransformer());
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
