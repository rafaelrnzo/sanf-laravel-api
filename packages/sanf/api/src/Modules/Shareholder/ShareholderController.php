<?php


namespace Sanf\Api\Modules\Shareholder;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;

class ShareholderController extends RestController
{
    public function postCreate(Request $request)
    {
        $this->validate($request, [
            "title" => ["required", "string"],
            "name" => ["required", "string"],
            "share_percentage" => ["required", "string"],
            "position" => ["nullable", "string"],
            "type" => ["required", "string", "in:C,P"],
        ]);
        return $this->responseOk();
    }

    public function getList(Request $request)
    {
        $result = [
            [
                "no" => "10",
                "title" => "MR.",
                "name" => "SANTOS IBRAHIM NOOR",
                "share_percentage" => "0",
                "position" => "DIREKTUR",
                'type' => "P"
            ],
            [
                "no" => "11",
                "title" => "PT",
                "name" => "TELADAN PRIMA AGRO",
                "share_percentage" => "99",
                "position" => "PEMEGANG SAHAM",
                "type" => "C"
            ]
        ];
        $result = json_decode(json_encode($result));
        return fractal($result, ShareholderTransformer::class);
    }

    public function getDetail(Request $request)
    {
        $result = [
                "no" => "10",
                "title" => "MR.",
                "name" => "SANTOS IBRAHIM NOOR",
                "share_percentage" => "0",
                "position" => "DIREKTUR",
                'type' => "P"
            ];
        $result = json_decode(json_encode($result));
        return fractal($result, ShareholderTransformer::class);
    }

    public function putUpdate(Request $request)
    {
        $this->validate($request, [
            "title" => ["required", "string"],
            "name" => ["required", "string"],
            "share_percentage" => ["required", "string"],
            "position" => ["nullable", "string"],
            "type" => ["required", "string", "in:C,P"],
        ]);
        return $this->responseOk();
    }

    public function delete($xid, $no)
    {
        return $this->responseOk();
    }
}
