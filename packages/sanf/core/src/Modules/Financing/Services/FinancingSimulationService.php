<?php

namespace Sanf\Core\Modules\Financing\Services;

use Exception;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\RequestFinancingSimulationDto;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;
use Sanf\Core\Modules\Financing\Enums\FirstInstallmentTypeEnum;
use Sanf\Core\Modules\Financing\Exceptions\FinancingGeneralException;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingMethodSpecificationFactoryInterface;

class FinancingSimulationService extends FinancingService implements ApplicationServiceInterface
{
    protected FinancingMethodSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingApplicationRepositoryInterface $financingApplicationRepository,
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingFacilityRepositoryInterface $financingFacilityRepository,
        FinancingMethodSpecificationFactoryInterface $specificationFactory
    ) {
        parent::__construct(
            $financingApplicationRepository,
            $financingMethodRepository,
            $financingPrerequisiteRepository,
            $financingFacilityRepository
        );
        $this->specificationFactory = $specificationFactory;
    }

    public function execute($dto = null)
    {
        /** @var RequestFinancingSimulationDto $dto */
        $financingMethod = $this->financingMethodRepository->findById($dto->financingMethodId);

        if (is_null($financingMethod)) {
            throw new FinancingGeneralException('Financing Method Not Found');
        }

        switch ($financingMethod->id) {
            case FinancingMethodEnum::SEWA_PEMBIAYAAN:
            case FinancingMethodEnum::JUAL_SEWA_BALIK:
            case FinancingMethodEnum::PEMBELIAN_ANGSURAN:
                return $this->calculateFinancingLease($dto);

            case FinancingMethodEnum::FASILITAS_MODAL_USAHA:
                return $this->calculateBusinessCapitalFacilities($dto);

            default:
                throw new Exception('Unknown financing method: ' . $financingMethod->id);
        }
    }

    private function calculateFinancingLease(RequestFinancingSimulationDto $dto)
    {
        $installmentInMonthAmount = $this->calculateInstallmentInMonthAmount($dto);
        $insuranceInCreditAmount = ($dto->firstYearInsuranceAmount / 12) * ($dto->tenor - 12);
        $totalCreditAmount = ($dto->unitAmount - $dto->downPaymentAmount) + $insuranceInCreditAmount;
        $firstPaymentAmount = $dto->downPaymentAmount + $dto->firstYearInsuranceAmount + $dto->adminFeeAmount + $dto->provisionAmount + $installmentInMonthAmount;

        return (object) [
            'unit_amount' => number_format($dto->unitAmount, 2, '.', ''),
            'down_payment_percentage' => number_format($dto->downPaymentPercentage, 2, '.', ''),
            'down_payment_amount' => number_format($dto->downPaymentAmount, 2, '.', ''),
            'first_installment_type' => $dto->firstInstallmentType,
            'interest_percentage' => number_format($dto->interestPercentage, 2, '.', ''),
            'tenor' => $dto->tenor,
            'first_year_insurance_amount' => number_format($dto->firstYearInsuranceAmount, 2, '.', ''),
            'admin_fee_amount' => number_format($dto->adminFeeAmount, 2, '.', ''),
            'provision_amount' => number_format($dto->provisionAmount, 2, '.', ''),
            'installment_per_month' => number_format($installmentInMonthAmount, 2, '.', ''),
            'credit_insurance_amount' => number_format($insuranceInCreditAmount, 2, '.', ''),
            'total_credit_amount' => number_format($totalCreditAmount, 2, '.', ''),
            'first_installment_amount' => number_format($installmentInMonthAmount, 2, '.', ''),
            'total_first_payment_amount' => number_format($firstPaymentAmount, 2, '.', ''),
        ];
    }

    private function calculateBusinessCapitalFacilities(RequestFinancingSimulationDto $dto)
    {

        // Logic installment_per_month
        $R = ($dto->interestPercentage * 100) / (12 * 100);

        $R1 = ($R + 1) ** $dto->tenor;

        // Calculation
        $calc = ($R + ($R / ($R1 - 1))) * $dto->financingAmount;

        return (object) [
            'financing_amount' => number_format($dto->financingAmount, 2, '.', ''),
            'tenor' => $dto->tenor,
            'installment_per_month' => number_format($calc, 2, '.', ''),
            'interest_percentage' => number_format($dto->interestPercentage, 2, '.', ''),
        ];
    }

    private function calculateInstallmentInMonthAmount(RequestFinancingSimulationDto $dto)
    {
        $monthlyInterest = $dto->interestPercentage / 1200;

        $presentValueAnnuity = 1 - pow(1 + $monthlyInterest, -$dto->tenor);

        $monthlyPayment = $dto->unitAmount * $monthlyInterest / $presentValueAnnuity;

        if ($dto->firstInstallmentType == FirstInstallmentTypeEnum::ADDM) {
            $monthlyPayment *= (1 + $monthlyInterest);
        }

        return $monthlyPayment;
    }
}
