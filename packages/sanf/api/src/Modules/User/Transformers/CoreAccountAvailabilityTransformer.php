<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

final class CoreAccountAvailabilityTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'email' => $dto->email,
            'has_mobile_account' => $dto->hasMobileAccount,
            'has_core_account' => $dto->hasCoreAccount,
            'core_accounts' => fractal($dto->coreAccounts)
                ->transformWith(CustomerProfileSimpleTransformer::class)
                ->serializeWith(ArraySerializer::class),
        ];
    }
}
