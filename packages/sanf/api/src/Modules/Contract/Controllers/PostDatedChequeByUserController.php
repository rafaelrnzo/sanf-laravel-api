<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\ContractPostDatedChequeTransformer;
use Sanf\Api\Modules\Contract\Transformers\PostDatedChequeTransformer;
use Sanf\Api\Modules\Contract\Transformers\PostDatedChequeV2Transformer;
use Sanf\Core\Modules\Contract\Dto\ContractPostDatedChequeDto;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeDto;
use Sanf\Core\Modules\Contract\Dto\PostDatedChequeV2Dto;
use Sanf\Core\Modules\Contract\Services\GetPostDatedChequeDetailService;
use Sanf\Core\Modules\Contract\Services\GetPostDatedChequeDetailV2Service;
use Sanf\Core\Modules\Contract\Services\ListContractPostDatedChequeService;
use Sanf\Core\Modules\PdcHold\Enums\CorePdcStatusEnum;

final class PostDatedChequeByUserController extends RestApiController
{
    public function getContract(
        Guard $auth,
        Request $request,
        $xid,
        ListContractPostDatedChequeService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
            'date_start' => ['nullable', 'string', 'date_format:Y-m-d'],
            'date_end' => ['nullable', 'string', 'date_format:Y-m-d'],
        ]);

        if (isset($input['date_start'])) {
            $input['date_start'] = CarbonImmutable::make($input['date_start']);
        }
        if (isset($input['date_end'])) {
            $input['date_end'] = CarbonImmutable::make($input['date_end']);
        }
        $dto = new ContractPostDatedChequeDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, ContractPostDatedChequeTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPDC(
        Guard $auth,
        $xid,
        $contract_no,
        Request $request,
        GetPostDatedChequeDetailService $service
    ) {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'], // @since CR2025
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new PostDatedChequeDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();
        $dto->contract_no = $contract_no;

        $result = $service->execute($dto);

        return fractal($result->data, PostDatedChequeTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPDCV2(
        Guard $auth,
        Request $request,
        GetPostDatedChequeDetailV2Service $service,
        $xid
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
            'status_id' => ['nullable', 'integer', Rule::in(CorePdcStatusEnum::values())],
            'date_start' => ['nullable', 'string', 'date_format:Y-m-d'],
            'date_end' => ['nullable', 'string', 'date_format:Y-m-d'],
            // in body
            'contract_no' => ['nullable', 'array'],
            'contract_no.*' => ['string', 'alpha_num'],
        ]);

        if (isset($input['date_start'])) {
            $input['date_start'] = CarbonImmutable::make($input['date_start']);
        }
        if (isset($input['date_end'])) {
            $input['date_end'] = CarbonImmutable::make($input['date_end']);
        }

        $dto = new PostDatedChequeV2Dto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, PostDatedChequeV2Transformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
