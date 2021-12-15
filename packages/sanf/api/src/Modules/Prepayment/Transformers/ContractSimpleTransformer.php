<?php

namespace Sanf\Api\Modules\Prepayment\Transformers;

use League\Fractal\TransformerAbstract;

final class ContractSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'contract_no' => $dto->contractNo,
            'is_submitted' => $dto->isSubmitted,
            'remaining_balance' => $dto->remainingBalance,
            'currency_type' => $dto->currencyType,
        ];
    }
}
