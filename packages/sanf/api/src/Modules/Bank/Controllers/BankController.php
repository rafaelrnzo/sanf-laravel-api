<?php

namespace Sanf\Api\Modules\Bank\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Bank\Transformers\UserBankAccountTransformer;
use Sanf\Core\Modules\Bank\Dtos\BrowseUserBankAccountRequestDto;
use Sanf\Core\Modules\Bank\Services\BrowseUserBankAccountService;

class BankController extends RestApiController
{
    public function getUserAccount(
        Guard $auth,
        Request $request,
        string $xid,
        BrowseUserBankAccountService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);

        $dto = new BrowseUserBankAccountRequestDto($input + ['profileXid' => $xid, 'userId' => $auth->id()]);

        $result = $service->execute($dto);

        return fractal($result->data, new UserBankAccountTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
