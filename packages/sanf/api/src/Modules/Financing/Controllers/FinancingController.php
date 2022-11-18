<?php


namespace Sanf\Api\Modules\Financing\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\FinancingListTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingPrerequisiteTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingSimulationTransformer;
use Sanf\Api\Modules\Financing\Transformers\GetFinancingCategoryTransformer;
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
        Guard $auth,
        Request $request,
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
            $dtoSendEmail = new SendEmailFinancingSimulationDto(
                [
                    'user_id' => $auth->id()
                ] + $simulationResult->toArray()
            );

            // Execute send email service
            $sendEmailService->execute($dtoSendEmail);
        }

        if ($dto->is_download_pdf) {
            // Set dto for download service
            $dtoDownload = new PdfFinancingSimulationRequestDto(
                [
                    'user_id' => $auth->id()
                ] + $simulationResult->toArray()
            );

            // Execute download service
            return $this->streamDownload(function () use ($downloadFinancingService, $dtoDownload) {
                echo $downloadFinancingService->execute($dtoDownload);
            }
                , 'SANFIND-Simulasi' . date('Y-m-d-H-i-s') . '.pdf'
            );
        }

        return fractal($simulationResult, new FinancingSimulationTransformer());
    }

    public function browseCategories(Request $request)
    {
        $input = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string', Rule::in(['oldest', 'latest',])],
        ]);

        $data = [];
        for ($index = 1; $index <= 10; $index++) {
            $data[] = (object)[
                'xid' => $index,
                'title' => 'Buldozer',
                'image_url' => 'https://via.placeholder.com/400x400.png?text=Image',
                'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                'button_label' => 'Lorem Ipsum',
                'created_at' => Carbon::now(),
            ];
        }

        $result = (object) [
            'data' => $data,
            'paginate'=> (object) [
                'total' => 10,
                'count' => count($data),
                'skip' => 0,
                'limit' => 10,
                'sort_by' => 'latest',
            ]
        ];

        return fractal($result->data, new GetFinancingCategoryTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }
}
