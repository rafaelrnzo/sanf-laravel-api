<?php


namespace Sanf\Api\Modules\Project;


use NbsPhp\Core\Controllers\AbstractController;
use Sanf\Core\Modules\Project\Services\ApproveProjectByExternalService;
use Sanf\Core\Modules\Project\Services\RejectProjectByExternalService;

class ProjectByExternalController extends AbstractController
{
    public function postApproveByExternal($xid, ApproveProjectByExternalService $service)
    {
        $service->execute((object)[
            'xid' => $xid
        ]);
        return redirect()->route('web-view.approval-project', ['status' => 'approve']);
    }

    public function postRejectByExternal($xid, RejectProjectByExternalService $service)
    {
        $service->execute((object)[
            'xid' => $xid
        ]);
        return redirect()->route('web-view.approval-project', ['status' => 'reject']);
    }
}
