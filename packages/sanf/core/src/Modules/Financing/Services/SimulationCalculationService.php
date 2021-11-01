<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\SimulationCalculationResultDto;
use Sanf\Core\Modules\Financing\Exceptions\FinancingGeneralException;
use Sanf\Core\Modules\Financing\Repositories\FinancingMethodRepositoryInterface;
use Sanf\Core\Modules\Financing\Repositories\FinancingPrerequisiteRepositoryInterface;
use Sanf\Core\Modules\Financing\Specifications\FinancingSpecificationFactoryInterface;

class SimulationCalculationService extends FinancingService implements ApplicationServiceInterface
{
    protected FinancingSpecificationFactoryInterface $specificationFactory;

    public function __construct(
        FinancingMethodRepositoryInterface $financingMethodRepository,
        FinancingPrerequisiteRepositoryInterface $financingPrerequisiteRepository,
        FinancingSpecificationFactoryInterface $specificationFactory
    )
    {
        parent::__construct($financingMethodRepository,$financingPrerequisiteRepository);
        $this->specificationFactory = $specificationFactory;
    }

    public function execute($dto = null)
    {
        // Get data financing method from repository
        $financing_method = $this->financingMethodRepository->get(
            $this->specificationFactory->findById($dto->financing_method_id)
        );

        if(is_null($financing_method)){
            throw new FinancingGeneralException('Financing Method Not Found');
        }

        // Logic installment_per_month
        $R = ($financing_method->interest_rate * 100) / (12 * 100) ;

        $R1 = pow(($R + 1), $dto->tenor_in_month);

        // Calculation
        $calc = ($R + ($R / ($R1 - 1))) * ($dto->financing_amount - $dto->down_payment_amount);

        // Formatting calculation
        $installment_per_month = number_format($calc, 2, '.', '');

        // Preparing result
        $result = [
            "financing_method_id" => (int)$dto->financing_method_id,
            "financing_method_name" => (string)$financing_method->name,
            "financing_amount" => (float)$dto->financing_amount,
            "down_payment_percentage" => (float)$dto->down_payment_percentage,
            "down_payment_amount" => (float)$dto->down_payment_amount,
            "tenor_in_month" => (int)$dto->tenor_in_month,
            "installment_per_month" => (float)$installment_per_month,
            "interest_rate_percentage" => (float)$financing_method->interest_rate
        ];

        // sent calculation;
        return new SimulationCalculationResultDto($result);

    }

}
