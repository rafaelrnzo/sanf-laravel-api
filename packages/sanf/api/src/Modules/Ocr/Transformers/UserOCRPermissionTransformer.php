<?php

namespace Sanf\Api\Modules\Ocr\Transformers;

use League\Fractal\TransformerAbstract;

class UserOCRPermissionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' =>  $dto->xid,
            'email' => $dto->email,
            'is_permitted' => $dto->isPermitted,
        ];
    }
}
