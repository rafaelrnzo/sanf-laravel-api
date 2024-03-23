<?php

namespace Sanf\External\Modules\Commodity;

use NbsPhp\Core\Controllers\AbstractController;
use Sanf\Core\Modules\Commodity\Services\ApproveCommodityByExternalService;
use Sanf\Core\Modules\Commodity\Services\RejectCommodityByExternalService;

class CommodityByExternalController extends AbstractController
{
    public function postApproveByExternal($xid, ApproveCommodityByExternalService $service)
    {
        $service->execute((object) [
            'xid' => $xid,
        ]);

        return redirect()->route('web-view.approval-commodity', ['status' => 'approve']);
    }

    public function postRejectByExternal($xid, RejectCommodityByExternalService $service)
    {
        $service->execute((object) [
            'xid' => $xid,
        ]);

        return redirect()->route('web-view.approval-commodity', ['status' => 'reject']);
    }
}
