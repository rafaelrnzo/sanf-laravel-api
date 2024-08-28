<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;

class ProvisionService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        if ($dto->financingMethodId === FinancingMethodEnum::PEMBELIAN_ANGSURAN) {
            $insuranceInCreditAmount = ((80 / 100) * $dto->firstYearInsuranceAmount / 12) * ($dto->tenor - 12);
            $netToFinance = ($dto->unitAmount - $dto->downPaymentAmount) + $insuranceInCreditAmount;

            $dto->provisionAmount = round((1 / 100) * $netToFinance);
            $dto->provisionAmount = ceil($dto->provisionAmount / 1000) * 1000;

            return $dto;
        }

        $insuranceInCreditAmount = ($dto->firstYearInsuranceAmount / 12) * ($dto->tenor - 12);
        $netToFinance = ($dto->unitAmount - $dto->downPaymentAmount) + $insuranceInCreditAmount;

        $dto->provisionAmount = round((1 / 100) * $netToFinance);
        $dto->provisionAmount = ceil($dto->provisionAmount / 1000) * 1000;

        return $dto;
    }
}
