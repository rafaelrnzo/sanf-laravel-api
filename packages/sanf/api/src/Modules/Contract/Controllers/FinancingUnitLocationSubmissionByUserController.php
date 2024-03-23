<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\ContractOfFinancingUnitSubmissionTransformer;
use Sanf\Api\Modules\Contract\Transformers\FinancingUnitSubmissionContractTransformer;
use Sanf\Core\Modules\Contract\Dto\ContractOfFinancingUnitSubmissionDto;
use Sanf\Core\Modules\Contract\Dtos\AddFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Services\AddFinancingUnitLocationSubmissionByUserService;
use Sanf\Core\Modules\Contract\Services\BrowseProcessFinancingUnitLocationSubmissionByUserService;
use Sanf\Core\Modules\Contract\Services\ListContractOfFinancingUnitSubmissionService;

final class FinancingUnitLocationSubmissionByUserController extends RestApiController
{
    public function getContract(Guard $auth, Request $request, $xid, ListContractOfFinancingUnitSubmissionService $service)
    {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new ContractOfFinancingUnitSubmissionDto($input + ['profile_xid' => $xid]);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, new ContractOfFinancingUnitSubmissionTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getFinancingUnitLocation(
        Guard $auth,
        $xid,
        $contract_no,
        Request $request,
        BrowseProcessFinancingUnitLocationSubmissionByUserService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto(
            $input + [
                'xid' => $contract_no,
                'profileXid' => $xid,
                'userId' => $auth->id(),
            ]
        );
        $dto->sortBy = Str::title($dto->sortBy);

        $result = $service->execute($dto);

        return fractal($result->data, FinancingUnitSubmissionContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postAdd(
        Guard $auth,
        $xid,
        $contract_no,
        $serial_no,
        Request $request,
        AddFinancingUnitLocationSubmissionByUserService $service
    ) {
        $input = $this->validate($request, [
            'brand_type_model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'string', 'max:255'],
            'location_metadata.city_id' => ['required', 'string', 'max:255'],
            'location_metadata.city_name' => ['required', 'string', 'max:255'],
            'submitted_location_metadata.city_id' => ['required', 'string', 'max:255'],
            'submitted_location_metadata.city_name' => ['required', 'string', 'max:255'],
        ]);
        $dto = new AddFinancingUnitLocationSubmissionByUserRequestDto(
            $input + [
                'userId' => $auth->id(),
                'profileXid' => $xid,
                'xid' => $contract_no,
                'serialNo' => $serial_no,
            ]
        );
        $dto->sortBy = Str::title($dto->sortBy);

        $service->execute($dto);

        return $this->responseOk();
    }
}
