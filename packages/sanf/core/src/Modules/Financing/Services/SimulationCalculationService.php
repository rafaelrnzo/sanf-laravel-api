<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\SimulationCalculationResultDto;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;
use Sanf\Core\Modules\Financing\Exceptions\FinancingGeneralException;
use Sanf\Core\Modules\Financing\Repositories\FinancingApplicationRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingFacilityRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingMethodSpecificationFactoryInterface;

class SimulationCalculationService extends FinancingService implements ApplicationServiceInterface
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
        // Get data financing method from repository
        $financing_method = $this->financingMethodRepository->findById($dto->financing_method_id);

        if (is_null($financing_method)) {
            throw new FinancingGeneralException('Financing Method Not Found');
        }

        // Logic installment_per_month
        $R = ($financing_method->interest_rate * 100) / (12 * 100);

        $R1 = ($R + 1) ** $dto->tenor_in_month;

        // Calculation
        $calc = ($R + ($R / ($R1 - 1))) * ($dto->financing_amount - $dto->down_payment_amount);
        $isAnjak = in_array($financing_method->id, [
            FinancingMethodEnum::ANJAK_PIUTANG_PEMBERIAN,
            FinancingMethodEnum::ANJAK_PIUTANG_TANPA_PEMBERIAN,
        ]);
        if ($isAnjak) {
            // formula = (total invoice-nilai retensi) - ((total invoice - nilai retensi)* % rate anjak piutang)
            //old
            $calc = ($dto->financing_amount - $dto->down_payment_amount) - (($dto->financing_amount - $dto->down_payment_amount) * $financing_method->interest_rate);
            //new
            //$calc = ($dto->financing_amount ) - (($dto->financing_amount ) * $financing_method->interest_rate) - $dto->down_payment_amount;
        }

        // Formatting calculation
        $installment_per_month = number_format($calc, 2, '.', '');

        $result = [
            'financing_method_id' => (int) $dto->financing_method_id,
            'financing_method_name' => (string) $financing_method->name,
            'financing_amount' => (float) $dto->financing_amount,
            'down_payment_percentage' => (int) $dto->down_payment_percentage,
            'down_payment_amount' => (float) $dto->down_payment_amount,
            'tenor_in_month' => (int) $dto->tenor_in_month,
            'installment_per_month' => (float) $installment_per_month,
            'interest_rate_percentage' => (int) ($financing_method->interest_rate * 100),
        ];

        return new SimulationCalculationResultDto($result);
    }
}
