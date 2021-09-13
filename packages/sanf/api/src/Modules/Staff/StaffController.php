<?php


namespace Sanf\Api\Modules\Staff;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\Staff\ActivateCompanyStaffService;
use Sanf\Core\Modules\Staff\DeactivateCompanyStaffService;
use Sanf\Core\Modules\Staff\GetListCompanyStaffService;
use Sanf\Core\Modules\Staff\GetListInvitedCompanyStaffService;

class StaffController extends RestApiController
{
    public function getList(Guard $auth, Request $request, $xid, GetListCompanyStaffService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = (object)($input + ['userId' => $auth->id(), 'xid' => $xid]);
        $result = $service->execute($dto);

        return fractal($result->data, new StaffTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getInvitedList(Guard $auth, Request $request, $xid, GetListInvitedCompanyStaffService $service)
    {
        //TODO DTO
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = (object)($input + ['userId' => $auth->id(), 'xid' => $xid]);
        $result = $service->execute($dto);

        return fractal($result->data, new StaffTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postActivate(Guard $auth, $xid, $no, ActivateCompanyStaffService $service)
    {
        //TODO DTO
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid,
            'no' => $no
        ];
        $service->execute($dto);
        return $this->responseOk();
    }

    public function postDeactivate(Guard $auth, $xid, $no, DeactivateCompanyStaffService $service)
    {
        //TODO DTO
        $dto = (object)[
            'userId' => $auth->id(),
            'xid' => $xid,
            'no' => $no
        ];
        $service->execute($dto);
        return $this->responseOk();
    }
}
