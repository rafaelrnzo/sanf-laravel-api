<?php

namespace Sanf\Api\Modules\Prepayment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Prepayment\Transformers\ContractSimpleTransformer;
use Sanf\Core\Modules\Prepayment\Dtos\BrowseContractByUserRequestDto;
use Sanf\Core\Modules\Prepayment\Services\BrowseContractByUserService;

final class ContractByUserController extends RestApiController
{
    public function getBrowse(Guard $auth, Request $request, $xid, BrowseContractByUserService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        $dto = new BrowseContractByUserRequestDto($input + [
                'profileXid' => $xid,
                'userId' => $auth->id()
            ]);
        $result = $service->execute($dto);

        return fractal($result->data, new ContractSimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
