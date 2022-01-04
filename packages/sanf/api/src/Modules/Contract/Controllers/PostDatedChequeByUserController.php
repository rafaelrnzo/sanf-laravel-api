<?php


namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\ContractPostDatedChequeTransformer;
use Sanf\Api\Modules\Contract\Transformers\PostDatedChequeTransformer;
use Sanf\Core\Modules\Contract\Dto\ContractPostDatedChequeDto;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeDto;
use Sanf\Core\Modules\Contract\Services\GetPostDatedChequeDetailService;
use Sanf\Core\Modules\Contract\Services\ListContractPostDatedChequeService;

final class PostDatedChequeByUserController extends RestApiController
{
    public function getContract(
        Guard $auth,
        Request $request,
        $xid,
        ListContractPostDatedChequeService $service
    ) {
        $input = $this->validate($request, [
            'contract_no' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new ContractPostDatedChequeDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, ContractPostDatedChequeTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPDC(
        Guard $auth,
        $contract_no,
        Request $request,
        GetPostDatedChequeDetailService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new PostDatedChequeDto($input);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();
        $dto->contract_no = $contract_no;

        $result = $service->execute($dto);

        return fractal($result->data, PostDatedChequeTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
