<?php


namespace Sanf\Api\Modules\Financing;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\FinancingListTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingSimulationTransformer;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodRequestDto;
use Sanf\Core\Modules\Financing\Dto\SimulationCalculationRequestDto;
use Sanf\Core\Modules\Financing\Services\ListFinancingMethodService;
use Sanf\Core\Modules\Financing\Services\SimulationCalculationService;

class FinancingController extends RestApiController
{
    public function getList(Request $request, ListFinancingMethodService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);

        $dto = new ListFinancingMethodRequestDto($input);

        $result = $service->execute($dto);

        return fractal($result->data, new FinancingListTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function calcSimulation(Request $request,SimulationCalculationService $calcService)
    {
        $input = $this->validate($request, [
            'financing_method_id' => ['required', 'integer'],
            'financing_amount' => ['required', 'numeric'],
            'down_payment_percentage' => ['required', 'numeric'],
            'down_payment_amount' => ['required', 'numeric'],
            'tenor_in_month' => ['required', 'integer'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $dto = new SimulationCalculationRequestDto($input);

        $result = $calcService->execute($dto);

        if($dto->is_send_email){
            // TODO: Create Service Send Email
        }

        if($dto->is_download_pdf){
            //TODO: Create Service Download PDF
        }

        return fractal($result,new FinancingSimulationTransformer());

    }

}
