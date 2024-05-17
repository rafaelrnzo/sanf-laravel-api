<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

final class RegisterCoreAccountTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'email' => $dto->email,
            'full_name' => $dto->fullName,
            'type_id' => $dto->typeId,
            'type_name' => $dto->typeName,
        ];
    }
}
