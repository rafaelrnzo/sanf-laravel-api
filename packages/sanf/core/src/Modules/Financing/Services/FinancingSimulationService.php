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

            case FinancingMethodEnum::ANJAK_PIUTANG_PEMBERIAN:
            case FinancingMethodEnum::ANJAK_PIUTANG_TANPA_PEMBERIAN:
                return $this->calculateFinancingFactoring($dto);

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
            'unit_amount' => (float) $dto->unitAmount,
            'down_payment_percentage' => (float) $dto->downPaymentPercentage,
            'down_payment_amount' => (float) $dto->downPaymentAmount,
            'first_installment_type' => $dto->firstInstallmentType,
            'interest_percentage' => (float) $dto->interestPercentage,
            'tenor' => $dto->tenor,
            'first_year_insurance_amount' => (float) $dto->firstYearInsuranceAmount,
            'admin_fee_amount' => (float) $dto->adminFeeAmount,
            'provision_amount' => (float) $dto->provisionAmount,
            'installment_per_month' => (float) number_format($installmentInMonthAmount, 2, '.', ''),
            'credit_insurance_amount' => (float) number_format($insuranceInCreditAmount, 2, '.', ''),
            'total_credit_amount' => (float) number_format($totalCreditAmount, 2, '.', ''),
            'first_installment_amount' => (float) number_format($installmentInMonthAmount, 2, '.', ''),
            'total_first_payment_amount' => (float) number_format($firstPaymentAmount, 2, '.', ''),
        ];
    }

    private function calculateBusinessCapitalFacilities(RequestFinancingSimulationDto $dto)
    {

        $R = ($dto->interestPercentage * 100) / (12 * 100);

        $R1 = ($R + 1) ** $dto->tenor;

        $calc = ($R + ($R / ($R1 - 1))) * $dto->financingAmount;

        return (object) [
            'financing_amount' => (float) $dto->financingAmount,
            'tenor' => $dto->tenor,
            'installment_per_month' => (float) number_format($calc, 2, '.', ''),
            'interest_percentage' => (float) $dto->interestPercentage,
        ];
    }

    private function calculateFinancingFactoring(RequestFinancingSimulationDto $dto)
    {
        $diskontoAmount = $dto->invoiceAmount * (($dto->interestPercentage / 100) / 360) * $dto->tenor;
        $disbursementAmount = $dto->invoiceAmount - $diskontoAmount - $dto->retentionAmount;

        return (object) [
            'invoice_amount' => (float) $dto->invoiceAmount,
            'interest_percentage' => (float) $dto->interestPercentage,
            'tenor' => $dto->tenor,
            'retention_amount' => (float) $dto->retentionAmount,
            'retention_percentage' => (float) $dto->retentionPercentage,
            'diskonto_amount' => (float) number_format($diskontoAmount, 2, '.', ''),
            'disbursement_amount' => (float) number_format($disbursementAmount, 2, '.', ''),
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
