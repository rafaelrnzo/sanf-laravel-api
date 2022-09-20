<?php

namespace Sanf\External\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseUserDeletionAccountTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->xid,
            'cust_id' => $dto->personalXid,
            'full_name' => $dto->fullName,
            'company_name' => $dto->companyName,
            'landline_number' => $dto->landlineNumber,
            'phone_number' => $dto->phoneNumber,
            'restore_expired_at' => unix_timestamp($dto->restoreExpiredAt),
            'created_at' => unix_timestamp($dto->createdAt),
        ];
    }
}
