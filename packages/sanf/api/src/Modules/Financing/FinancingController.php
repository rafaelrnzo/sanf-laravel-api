<?php


namespace Sanf\Api\Modules\Financing;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\FinancingListTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingSimulationTransformer;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodRequestDto;
use Sanf\Core\Modules\Financing\Dto\SendEmailFinancingDto;
use Sanf\Core\Modules\Financing\Dto\SimulationCalculationRequestDto;
use Sanf\Core\Modules\Financing\Services\ListFinancingMethodService;
use Sanf\Core\Modules\Financing\Services\SendEmailFinancingSimulationService;
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

    public function calcSimulation(Guard $auth, Request $request, SimulationCalculationService $calcService, SendEmailFinancingSimulationService $sendEmailService)
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

        if ($dto->is_send_email) {

            $dtoSendEmail = new SendEmailFinancingDto([
                'result' => $result,
                'userId' => $auth->id()
            ]);

            $sendEmailService->execute($dtoSendEmail);
        }

        if ($dto->is_download_pdf) {
            //TODO: Create Service Download PDF
            return $this->responseOk();
        }

        return fractal($result, new FinancingSimulationTransformer());

    }

}
