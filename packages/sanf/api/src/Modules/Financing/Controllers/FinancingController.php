<?php


namespace Sanf\Api\Modules\Financing\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\FinancingListTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingPrerequisiteTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingSimulationTransformer;
use Sanf\Core\Modules\Financing\Dto\ListFinancingFacilityRequestDto;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodByFacilityRequestDto;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodRequestDto;
use Sanf\Core\Modules\Financing\Dto\ListFinancingPrerequisiteRequestDto;
use Sanf\Core\Modules\Financing\Dto\PdfFinancingSimulationRequestDto;
use Sanf\Core\Modules\Financing\Dto\SendEmailFinancingSimulationDto;
use Sanf\Core\Modules\Financing\Dto\SimulationCalculationRequestDto;
use Sanf\Core\Modules\Financing\Services\GetPdfFinancingSimulationService;
use Sanf\Core\Modules\Financing\Services\ListFinancingFacilityService;
use Sanf\Core\Modules\Financing\Services\ListFinancingMethodByFacilityService;
use Sanf\Core\Modules\Financing\Services\ListFinancingMethodService;
use Sanf\Core\Modules\Financing\Services\ListFinancingPrerequisiteService;
use Sanf\Core\Modules\Financing\Services\SendEmailFinancingSimulationService;
use Sanf\Core\Modules\Financing\Services\SimulationCalculationService;
use function fractal;

class FinancingController extends RestApiController
{
    public function getListFacilities(Request $request, ListFinancingFacilityService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
        ]);

        $dto = new ListFinancingFacilityRequestDto($input);

        $result = $service->execute($dto);

        return fractal($result->data, new FinancingListTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getListMethodsByFacility(Request $request, $id, ListFinancingMethodByFacilityService $service)
    {
        // Validate request
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
        ]);

        // Insert id to array input
        $input = array_merge($input, ['id' => (int)$id]);

        $dto = new ListFinancingMethodByFacilityRequestDto($input);

        $result = $service->execute($dto);

        return fractal($result->data, new FinancingListTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getPrerequisiteList(Request $request, ListFinancingPrerequisiteService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
        ]);

        $dto = new ListFinancingPrerequisiteRequestDto($input);

        $result = $service->execute($dto);

        return fractal($result->data, new FinancingPrerequisiteTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getListMethods(Request $request, ListFinancingMethodService $service)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
        ]);

        $dto = new ListFinancingMethodRequestDto($input);

        $result = $service->execute($dto);

        return fractal($result->data, new FinancingListTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function postCalculateSimulation(
        Guard $auth, Request $request,
        SimulationCalculationService $calcService,
        SendEmailFinancingSimulationService $sendEmailService,
        GetPdfFinancingSimulationService $downloadFinancingService
    ) {
        $input = $this->validate($request, [
            'financing_method_id' => ['required', 'integer'],
            'financing_amount' => ['required', 'numeric'],
            'down_payment_percentage' => ['required', 'integer'],
            'down_payment_amount' => ['required', 'numeric'],
            'tenor_in_month' => ['required', 'integer'],
            'is_send_email' => ['required'],
            'is_download_pdf' => ['required'],
        ]);
        $dto = new SimulationCalculationRequestDto($input);

        $simulationResult = $calcService->execute($dto);

        if ($dto->is_send_email) {
            // Set dto for send email service
            $dtoSendEmail = new SendEmailFinancingSimulationDto([
                    'user_id' => $auth->id()
                ] + $simulationResult->toArray());

            // Execute send email service
            $sendEmailService->execute($dtoSendEmail);
        }

        if ($dto->is_download_pdf) {
            // Set dto for download service
            $dtoDownload = new PdfFinancingSimulationRequestDto([
                    'user_id' => $auth->id()
                ] + $simulationResult->toArray());

            // Execute download service
            return $this->streamDownload(function () use ($downloadFinancingService, $dtoDownload) {
                echo $downloadFinancingService->execute($dtoDownload);
            }
                , 'SANFIND-Simulasi' . date('Y-m-d-H-i-s') . '.pdf'
            );
        }

        return fractal($simulationResult, new FinancingSimulationTransformer());
    }
}
