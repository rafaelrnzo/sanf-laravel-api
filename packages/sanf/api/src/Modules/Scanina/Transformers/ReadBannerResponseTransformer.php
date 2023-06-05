<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class ReadBannerResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        return [
            'image_url' => $dto->image_url,
        ];
    }
}
