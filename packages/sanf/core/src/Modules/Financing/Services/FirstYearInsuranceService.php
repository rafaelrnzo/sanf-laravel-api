<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;

class FirstYearInsuranceService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        if ($dto->financingMethodId === FinancingMethodEnum::PEMBELIAN_ANGSURAN) {
            $dto->firstYearInsuranceAmount = ((2.39 / 100) * $dto->unitAmount) + 50000;
            $dto->firstYearInsuranceAmount = ceil($dto->firstYearInsuranceAmount / 1000) * 1000;

            return $dto;
        }

        $dto->firstYearInsuranceAmount = ((1.1 / 100) * $dto->unitAmount) + 50000;
        $dto->firstYearInsuranceAmount = ceil($dto->firstYearInsuranceAmount / 1000) * 1000;

        return $dto;
    }
}
