<?php


namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\AccountReceivableContractTransformer;
use Sanf\Core\Modules\Contract\Dto\AccountReceivableContractDto;
use Sanf\Core\Modules\Contract\Services\ListAccountReceivableContractService;

final class AccountReceivableByUserController extends RestApiController
{
    public function getInfo(
        Guard $auth,
        Request $request,
        $xid,
        ListAccountReceivableContractService $service
    ) {
        $input = $this->validate($request, [
            'currency_type' => ['nullable', 'in:IDR,USD'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new AccountReceivableContractDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, AccountReceivableContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
