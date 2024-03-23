<?php

namespace NbsPhp\Core\Transformers;

use League\Fractal\TransformerAbstract;

class LoginTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'profile' => [
                'id' => $item->id,
                'full_name' => $item->full_name,
                'email' => $item->username,
                'avatar_url' => file_get_url($item->avatar_image),
            ],
        ];
    }
}
