<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\DetailContractTransformer;
use Sanf\Api\Modules\Contract\Transformers\FinancingUnitContractTransformer;
use Sanf\Api\Modules\Contract\Transformers\ListContractTransformer;
use Sanf\Api\Modules\Contract\Transformers\SummaryBillContractTransformer;
use Sanf\Core\Modules\Contract\Dto\FinancingUnitContractDto;
use Sanf\Core\Modules\Contract\Dto\ListContractDto;
use Sanf\Core\Modules\Contract\Dto\SummaryBillContractDto;
use Sanf\Core\Modules\Contract\Enums\ContractTypeEnum;
use Sanf\Core\Modules\Contract\Services\GetContractDetailService;
use Sanf\Core\Modules\Contract\Services\GetFinancingUnitContractService;
use Sanf\Core\Modules\Contract\Services\ListContractService;
use Sanf\Core\Modules\Contract\Services\SummaryBillContractService;

final class ContractFinancingUnitByUserController extends RestApiController
{
    public function getList(
        Guard $auth,
        Request $request,
        $xid,
        ListContractService $service
    ) {
        $input = $this->validate($request, [
            'contract_type' => ['nullable', 'in:active,settled'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new ListContractDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        switch ($input['contract_type']) {
            case ContractTypeEnum::SETTLED:
                $dto->contract_type = ContractTypeEnum::SETTLED_LABEL;
                break;
            case ContractTypeEnum::ACTIVE_LABEL:
            default:
                $dto->contract_type = ContractTypeEnum::ACTIVE_LABEL;
                break;
        }

        $result = $service->execute($dto);

        return fractal($result->data, ListContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getDetail(Guard $auth, $xid, $contract_no, GetContractDetailService $service)
    {
        $dto = (object)[
            'user_id' => $auth->id(),
            'profile_xid' => $xid,
            'contract_no' => $contract_no,
        ];

        $result = $service->execute($dto);

        return fractal($result, DetailContractTransformer::class);
    }

    public function getFinancingUnit(
        Guard $auth,
        $xid,
        $contract_no,
        Request $request,
        GetFinancingUnitContractService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new FinancingUnitContractDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();
        $dto->contract_no = $contract_no;

        $result = $service->execute($dto);

        return fractal($result->data, FinancingUnitContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPenalties(
        Guard $auth,
        $xid,
        $contract_no,
        Request $request,
        SummaryBillContractService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new SummaryBillContractDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();
        $dto->contract_no = $contract_no;

        $result = $service->execute($dto);

        return fractal($result->data, SummaryBillContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
