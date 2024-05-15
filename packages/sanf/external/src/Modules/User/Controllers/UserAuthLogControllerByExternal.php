<?php

namespace Sanf\External\Modules\User\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Dto\BrowseRequestDto;
use NbsPhp\Core\Services\TransactionalApplicationService;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Core\Modules\User\Enums\UserAuthLogStatusEnum;
use Sanf\Core\Modules\User\Services\ApproveDeactivateAccountService;
use Sanf\Core\Modules\User\Services\BrowseUserDeletionAccountService;
use Sanf\Core\Modules\User\Services\RejectDeactivateAccountService;
use Sanf\External\Modules\User\Transformers\BrowseUserDeletionAccountTransformer;

class UserAuthLogControllerByExternal extends RestApiController
{
    public function getBrowse(Request $request, BrowseUserDeletionAccountService $service)
    {
        $input = $this->validate($request, [
            'keyboard' => 'nullable|string|max:255',
            'limit' => 'nullable|integer',
            'skip' => 'nullable|integer',
            'sort_by' => ['nullable', Rule::in(['asc', 'desc'])],
            'status_id' => ['nullable', Rule::in(UserAuthLogStatusEnum::ALL)],
        ]);

        $dto = new BrowseRequestDto($input);
        $dto->statusId = $input['status_id'] ?? UserAuthLogStatusEnum::SUBMIT;

        $result = $service->execute($dto);

        return fractal($result->data, BrowseUserDeletionAccountTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postApprove(
        string $xid,
        Request $request,
        ApproveDeactivateAccountService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input = $this->validate($request, [
            'user_id' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
        ]);

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $transactionalService->execute((object) array_merge($input, ['xid' => $xid]));

        return $this->responseOk();
    }

    public function postReject(
        string $xid,
        Request $request,
        RejectDeactivateAccountService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input = $this->validate($request, [
            'user_id' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $transactionalService->execute((object) array_merge($input, ['xid' => $xid]));

        return $this->responseOk();
    }
}
