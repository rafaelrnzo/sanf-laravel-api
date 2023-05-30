<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyResponseDto;

class BrowseRegionResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
        ];
    }
}
