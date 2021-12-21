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
use Sanf\Core\Modules\Contract\Dto\CreateFinancingUnitSubmissionDto;
use Sanf\Core\Modules\Contract\Dto\FinancingUnitSubmissionDto;
use Sanf\Core\Modules\Contract\Services\GetFinancingUnitSubmissionContractService;
use Sanf\Core\Modules\Contract\Services\ListContractOfFinancingUnitSubmissionService;

class FinancingUnitSubmissionController extends RestApiController
{
    public function getContractList(
        Guard $auth,
        Request $request,
        ListContractOfFinancingUnitSubmissionService $service
    ) {
        $input = $this->validate($request, [
            'contract_no' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new ContractOfFinancingUnitSubmissionDto($input);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();

        $result = $service->execute($dto);

        return fractal($result->data, ContractOfFinancingUnitSubmissionTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getFinancingUnit(
        Guard $auth,
        $contract_no,
        Request $request,
        GetFinancingUnitSubmissionContractService $service
    ) {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer', 'max:99'],
            'limit' => ['nullable', 'integer', 'max:99'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
        ]);

        $dto = new FinancingUnitSubmissionDto($input);
        $dto->sort_by = Str::title($dto->sort_by);
        $dto->user_id = $auth->id();
        $dto->contract_no = $contract_no;

        $result = $service->execute($dto);

        return fractal($result->data, FinancingUnitSubmissionContractTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function updateLocation(Guard $auth, $contract_no, $serial_no, Request $request)
    {
        $input = $this->validate($request, [
            'location_metadata.city_id' => ['required', 'string', 'max:255'],
            'location_metadata.city_name' => ['required', 'string', 'max:255'],
        ]);

        $input['contract_no'] = $contract_no;
        $input['serial_no'] = $serial_no;

        $dto = new CreateFinancingUnitSubmissionDto($input);

        return $this->responseOk();
    }
}
