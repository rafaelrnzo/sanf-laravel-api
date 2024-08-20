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
            case FinancingMethodEnum::PEMBELIAN_ANGSURAN:
            case FinancingMethodEnum::JUAL_SEWA_BALIK:
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
        $firstInstallmentAmount = $installmentInMonthAmount;

        if ($dto->firstInstallmentType == FirstInstallmentTypeEnum::ADDB) {
            $installmentInMonthAmount = 0;
        }

        $insuranceInCreditAmount = ($dto->firstYearInsuranceAmount / 12) * ($dto->tenor - 12);
        $totalCreditAmount = ceil(($dto->unitAmount - $dto->downPaymentAmount) + $insuranceInCreditAmount);
        $firstPaymentAmount = ceil($dto->downPaymentAmount + $dto->firstYearInsuranceAmount + $dto->adminFeeAmount + $dto->provisionAmount + $installmentInMonthAmount);

        return (object) [
            'financing_method_id' => $dto->financingMethodId,
            'unit_amount' => (int) $dto->unitAmount,
            'down_payment_percentage' => (int) $dto->downPaymentPercentage,
            'down_payment_amount' => (int) $dto->downPaymentAmount,
            'first_installment_type' => $dto->firstInstallmentType,
            'interest_percentage' => (int) $dto->interestPercentage,
            'tenor' => $dto->tenor,
            'first_year_insurance_amount' => (int) $dto->firstYearInsuranceAmount,
            'admin_fee_amount' => (int) $dto->adminFeeAmount,
            'provision_amount' => (int) $dto->provisionAmount,
            'installment_per_month' => (int) number_format($installmentInMonthAmount, 2, '.', ''),
            'credit_insurance_amount' => (int) number_format($insuranceInCreditAmount, 2, '.', ''),
            'total_credit_amount' => (int) number_format($totalCreditAmount, 2, '.', ''),
            'first_installment_amount' => (int) number_format($installmentInMonthAmount, 2, '.', ''),
            'total_first_payment_amount' => (int) number_format($firstPaymentAmount, 2, '.', ''),
        ];
    }

    private function calculateBusinessCapitalFacilities(RequestFinancingSimulationDto $dto)
    {
        $monthlyInterest = $dto->interestPercentage / 1200;
        $presentValueAnnuity = 1 - pow(1 + $monthlyInterest, -$dto->tenor);

        $calc = ceil($dto->financingAmount * $monthlyInterest / $presentValueAnnuity);

        return (object) [
            'financing_method_id' => $dto->financingMethodId,
            'financing_amount' => (int) $dto->financingAmount,
            'tenor' => $dto->tenor,
            'installment_per_month' => (int) number_format($calc, 2, '.', ''),
            'interest_percentage' => (int) $dto->interestPercentage,
        ];
    }

    private function calculateFinancingFactoring(RequestFinancingSimulationDto $dto)
    {
        $diskontoAmount = $dto->invoiceAmount * (($dto->interestPercentage / 100) / 360) * $dto->tenor;
        $disbursementAmount = ceil($dto->invoiceAmount - $diskontoAmount - $dto->retentionAmount);

        return (object) [
            'financing_method_id' => $dto->financingMethodId,
            'invoice_amount' => (int) $dto->invoiceAmount,
            'interest_percentage' => (int) $dto->interestPercentage,
            'tenor' => $dto->tenor,
            'retention_amount' => (int) $dto->retentionAmount,
            'retention_percentage' => (int) $dto->retentionPercentage,
            'diskonto_amount' => (int) number_format($diskontoAmount, 2, '.', ''),
            'disbursement_amount' => (int) number_format($disbursementAmount, 2, '.', ''),
        ];
    }

    private function calculateInstallmentInMonthAmount(RequestFinancingSimulationDto $dto)
    {
        $monthlyInterest = $dto->interestPercentage / 1200;

        $presentValueAnnuity = 1 - pow(1 + $monthlyInterest, -$dto->tenor);

        $monthlyPayment = $dto->unitAmount * $monthlyInterest / $presentValueAnnuity;

        if ($dto->firstInstallmentType == FirstInstallmentTypeEnum::ADDM) {
            $monthlyPayment = $monthlyPayment / (1 + $monthlyInterest);
        }

        return ceil($monthlyPayment);
    }
}
