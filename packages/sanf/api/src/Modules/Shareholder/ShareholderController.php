<?php


namespace Sanf\Api\Modules\Shareholder;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\User\CreateShareholderService;
use Sanf\Core\Modules\User\GetListShareholderService;
use Spatie\DataTransferObject\DataTransferObject;

class ShareholderController extends RestController
{
    public function postCreate(Request $request, CreateShareholderService $service, string $xid)
    {
        $this->validate($request, [
            "title" => ["required", "string"],
            "name" => ["required", "string"],
            "percentage" => ["required", "string"],
            "position" => ["nullable", "string"],
            "type" => ["required", "string", "in:C,P"],
        ]);

        $dto = new CreateShareholderDto([
            "id" => $xid,
            "title" => $request->input('title'),
            "name" => $request->input('name'),
            "job" => $request->input('position'),
            "percentage" => $request->input('percentage'),
            "type" => $request->input('type'),
        ]);

        $result = $service->execute($dto);

        return $this->responseOk();
    }

    public function getList(GetListShareholderService $service, string $xid)
    {
        $dto = new GetListShareholderDto([
            'xid' => $xid
        ]);
        $result = $service->execute($dto);

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
