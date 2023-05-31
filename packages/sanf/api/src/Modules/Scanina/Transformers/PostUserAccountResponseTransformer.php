<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class PostUserAccountResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        $user = optional($dto->user);
        return [
            'isRegistered' => (bool)optional($dto)->isRegistered,
            'isVerified' => !empty($user) && !empty($user->emailVerifiedAt),
        ];
    }
}
