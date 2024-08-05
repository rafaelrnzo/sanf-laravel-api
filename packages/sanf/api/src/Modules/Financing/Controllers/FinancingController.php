<?php

namespace Sanf\Api\Modules\Financing\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Dto\BrowseRequestDto;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Financing\Transformers\FinancingListTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingPrerequisiteTransformer;
use Sanf\Api\Modules\Financing\Transformers\FinancingSimulationTransformer;
use Sanf\Api\Modules\Financing\Transformers\GetFinancingCategoryTransformer;
use Sanf\Api\Modules\Financing\Transformers\ResponseFinancingSimulationTransformer;
use Sanf\Api\Modules\Financing\Transformers\ResponseFirstYearInsuranceTransformer;
use Sanf\Api\Modules\Financing\Transformers\ResponseProvisionTransformer;
use Sanf\Core\Modules\Financing\Dto\ListFinancingFacilityRequestDto;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodByFacilityRequestDto;
use Sanf\Core\Modules\Financing\Dto\ListFinancingMethodRequestDto;
use Sanf\Core\Modules\Financing\Dto\ListFinancingPrerequisiteRequestDto;
use Sanf\Core\Modules\Financing\Dto\PdfFinancingSimulationRequestDto;
use Sanf\Core\Modules\Financing\Dto\RequestFinancingSimulationDto;
use Sanf\Core\Modules\Financing\Dto\SendEmailFinancingSimulationDto;
use Sanf\Core\Modules\Financing\Dto\SimulationCalculationRequestDto;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;
use Sanf\Core\Modules\Financing\Enums\FirstInstallmentTypeEnum;
use Sanf\Core\Modules\Financing\Services\BrowseFinancingCategoryService;
use Sanf\Core\Modules\Financing\Services\FinancingSimulationService;
use Sanf\Core\Modules\Financing\Services\FirstYearInsuranceService;
use Sanf\Core\Modules\Financing\Services\GetPdfFinancingSimulationService;
use Sanf\Core\Modules\Financing\Services\ListFinancingFacilityService;
use Sanf\Core\Modules\Financing\Services\ListFinancingMethodByFacilityService;
use Sanf\Core\Modules\Financing\Services\ListFinancingMethodService;
use Sanf\Core\Modules\Financing\Services\ListFinancingPrerequisiteService;
use Sanf\Core\Modules\Financing\Services\ProvisionService;
use Sanf\Core\Modules\Financing\Services\SendEmailFinancingSimulationService;
use Sanf\Core\Modules\Financing\Services\SimulationCalculationService;

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
        $input = array_merge($input, ['id' => (int) $id]);

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
                    'user_id' => $auth->id(),
                ] + $simulationResult->toArray()
            );

            // Execute send email service
            $sendEmailService->execute($dtoSendEmail);
        }

        if ($dto->is_download_pdf) {
            // Set dto for download service
            $dtoDownload = new PdfFinancingSimulationRequestDto(
                [
                    'user_id' => $auth->id(),
                ] + $simulationResult->toArray()
            );

            // Execute download service
            return $this->streamDownload(
                function () use ($downloadFinancingService, $dtoDownload) {
                    echo $downloadFinancingService->execute($dtoDownload);
                },
                'SANFIND-Simulasi' . date('Y-m-d-H-i-s') . '.pdf'
            );
        }

        return fractal($simulationResult, new FinancingSimulationTransformer());
    }

    public function browseCategories(Request $request, BrowseFinancingCategoryService $service)
    {
        $input = $this->validate($request, [
            'keyword' => ['nullable', 'string', 'max:255'],
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string', Rule::in(['oldest', 'latest'])],
        ]);

        $dto = new BrowseRequestDto($input);
        $result = $service->execute($dto);

        return fractal($result->data, new GetFinancingCategoryTransformer())
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function calculateFinancingLease(Request $request, FinancingSimulationService $service)
    {
        $input = $this->validate($request, [
            'unit_amount' => ['required', 'numeric'],
            'down_payment_percentage' => ['required', 'integer'],
            'down_payment_amount' => ['required', 'numeric'],
            'first_installment_type' => ['required', Rule::in(FirstInstallmentTypeEnum::ALL)],
            'interest_percentage' => ['required', 'integer'],
            'tenor' => ['required', 'integer'],
            'first_year_insurance_amount' => ['required', 'numeric'],
            'admin_fee_amount' => ['required', 'numeric'],
            'provision_amount' => ['required', 'numeric'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $input['unit_amount'] = (float) $request->get('unit_amount');
        $input['down_payment_amount'] = (float) $request->get('down_payment_amount');
        $input['first_year_insurance_amount'] = (float) $request->get('first_year_insurance_amount');
        $input['admin_fee_amount'] = (float) $request->get('down_payment_amount');
        $input['provision_amount'] = (float) $request->get('provision_amount');
        $input['financing_method_id'] = FinancingMethodEnum::SEWA_PEMBIAYAAN;
        $requestSimulationDto = new RequestFinancingSimulationDto($input);

        $simulationResult = $service->execute($requestSimulationDto);

        return fractal($simulationResult, new ResponseFinancingSimulationTransformer());
    }

    public function calculateCreditBuying(Request $request, FinancingSimulationService $service)
    {
        $input = $this->validate($request, [
            'unit_amount' => ['required', 'numeric'],
            'down_payment_percentage' => ['required', 'integer'],
            'down_payment_amount' => ['required', 'numeric'],
            'first_installment_type' => ['required', Rule::in(FirstInstallmentTypeEnum::ALL)],
            'interest_percentage' => ['required', 'integer'],
            'tenor' => ['required', 'integer'],
            'first_year_insurance_amount' => ['required', 'numeric'],
            'admin_fee_amount' => ['required', 'numeric'],
            'provision_amount' => ['required', 'numeric'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $input['unit_amount'] = (float) $request->get('unit_amount');
        $input['down_payment_amount'] = (float) $request->get('down_payment_amount');
        $input['first_year_insurance_amount'] = (float) $request->get('first_year_insurance_amount');
        $input['admin_fee_amount'] = (float) $request->get('down_payment_amount');
        $input['provision_amount'] = (float) $request->get('provision_amount');
        $input['financing_method_id'] = FinancingMethodEnum::PEMBELIAN_ANGSURAN;
        $requestSimulationDto = new RequestFinancingSimulationDto($input);

        $simulationResult = $service->execute($requestSimulationDto);

        return fractal($simulationResult, new ResponseFinancingSimulationTransformer());
    }

    public function calculateSaleLeaseBack(Request $request, FinancingSimulationService $service)
    {
        $input = $this->validate($request, [
            'unit_amount' => ['required', 'numeric'],
            'down_payment_percentage' => ['required', 'integer'],
            'down_payment_amount' => ['required', 'numeric'],
            'first_installment_type' => ['required', Rule::in(FirstInstallmentTypeEnum::ALL)],
            'interest_percentage' => ['required', 'integer'],
            'tenor' => ['required', 'integer'],
            'first_year_insurance_amount' => ['required', 'numeric'],
            'admin_fee_amount' => ['required', 'numeric'],
            'provision_amount' => ['required', 'numeric'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $input['unit_amount'] = (float) $request->get('unit_amount');
        $input['down_payment_amount'] = (float) $request->get('down_payment_amount');
        $input['first_year_insurance_amount'] = (float) $request->get('first_year_insurance_amount');
        $input['admin_fee_amount'] = (float) $request->get('down_payment_amount');
        $input['provision_amount'] = (float) $request->get('provision_amount');
        $input['financing_method_id'] = FinancingMethodEnum::JUAL_SEWA_BALIK;
        $requestSimulationDto = new RequestFinancingSimulationDto($input);

        $simulationResult = $service->execute($requestSimulationDto);

        return fractal($simulationResult, new ResponseFinancingSimulationTransformer());
    }

    public function calculateBusinessCapitalFacilities(Request $request, FinancingSimulationService $service)
    {
        $input = $this->validate($request, [
            'financing_amount' => ['required', 'numeric'],
            'interest_percentage' => ['required', 'integer'],
            'tenor' => ['required', 'integer'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $input['financing_amount'] = (float) $request->get('financing_amount');
        $input['financing_method_id'] = FinancingMethodEnum::FASILITAS_MODAL_USAHA;
        $requestSimulationDto = new RequestFinancingSimulationDto($input);

        $simulationResult = $service->execute($requestSimulationDto);

        return fractal($simulationResult, new ResponseFinancingSimulationTransformer());
    }

    public function calculateCollateralFactoring(Request $request, FinancingSimulationService $service)
    {
        $input = $this->validate($request, [
            'invoice_amount' => ['required', 'numeric'],
            'interest_percentage' => ['required', 'integer'],
            'retention_percentage' => ['required', 'integer'],
            'retention_amount' => ['required', 'integer'],
            'tenor' => ['required', 'integer'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $input['invoice_amount'] = (float) $request->get('invoice_amount');
        $input['retention_amount'] = (float) $request->get('retention_amount');
        $input['financing_method_id'] = FinancingMethodEnum::ANJAK_PIUTANG_PEMBERIAN;
        $requestSimulationDto = new RequestFinancingSimulationDto($input);

        $simulationResult = $service->execute($requestSimulationDto);

        return fractal($simulationResult, new ResponseFinancingSimulationTransformer());
    }

    public function calculateUnSecuredFactoring(Request $request, FinancingSimulationService $service)
    {
        $input = $this->validate($request, [
            'invoice_amount' => ['required', 'numeric'],
            'interest_percentage' => ['required', 'integer'],
            'retention_percentage' => ['required', 'integer'],
            'retention_amount' => ['required', 'integer'],
            'tenor' => ['required', 'integer'],
            'is_send_email' => ['required', 'boolean'],
            'is_download_pdf' => ['required', 'boolean'],
        ]);

        $input['invoice_amount'] = (float) $request->get('invoice_amount');
        $input['retention_amount'] = (float) $request->get('retention_amount');
        $input['financing_method_id'] = FinancingMethodEnum::ANJAK_PIUTANG_TANPA_PEMBERIAN;
        $requestSimulationDto = new RequestFinancingSimulationDto($input);

        $simulationResult = $service->execute($requestSimulationDto);

        return fractal($simulationResult, new ResponseFinancingSimulationTransformer());
    }

    public function calculateFirstYearInsurance(Request $request, FirstYearInsuranceService $service)
    {
        $input = $this->validate($request, [
            'financing_method_id' => ['required', Rule::in([FinancingMethodEnum::SEWA_PEMBIAYAAN, FinancingMethodEnum::JUAL_SEWA_BALIK, FinancingMethodEnum::PEMBELIAN_ANGSURAN])],
            'financing_method_name' => ['required', 'string'],
            'unit_amount' => ['required', 'numeric'],
        ]);

        $requestFirstYearInsuranceDto = (object) [
            'financingMethodId' => $request->get('financing_method_id'),
            'financingMethodName' => $request->get('financing_method_name'),
            'unitAmount' => $request->get('unit_amount'),
        ];

        $result = $service->execute($requestFirstYearInsuranceDto);

        return fractal($result, ResponseFirstYearInsuranceTransformer::class);
    }

    public function calculateProvision(Request $request, ProvisionService $service)
    {
        $input = $this->validate($request, [
            'financing_method_id' => ['required', Rule::in([FinancingMethodEnum::SEWA_PEMBIAYAAN, FinancingMethodEnum::JUAL_SEWA_BALIK, FinancingMethodEnum::PEMBELIAN_ANGSURAN])],
            'financing_method_name' => ['required', 'string'],
            'unit_amount' => ['required', 'numeric'],
            'down_payment_percentage' => ['required', 'integer'],
            'down_payment_amount' => ['required', 'numeric'],
            'tenor' => ['required', 'integer'],
            'first_year_insurance_amount' => ['required', 'numeric'],
        ]);

        $requestProvisionDto = (object) [
            'financingMethodId' => $request->get('financing_method_id'),
            'financingMethodName' => $request->get('financing_method_name'),
            'unitAmount' => $request->get('unit_amount'),
            'downPaymentPercentage' => $request->get('down_payment_percentage'),
            'downPaymentAmount' => $request->get('down_payment_amount'),
            'tenor' => $request->get('tenor'),
            'firstYearInsuranceAmount' => $request->get('first_year_insurance_amount'),
        ];

        $result = $service->execute($requestProvisionDto);

        return fractal($result, ResponseProvisionTransformer::class);
    }
}
