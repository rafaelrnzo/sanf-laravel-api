<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductFilterResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string) optional($dto)->name,
            'icon_url' => (string) optional($dto)->iconUrl,
        ];
    }
}
