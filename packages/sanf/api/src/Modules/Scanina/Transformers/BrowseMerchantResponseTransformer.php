<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\BrowseMerchantResponseDto;

class BrowseMerchantResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var BrowseMerchantResponseDto $dto */
        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->shopName,
        ];
    }
}
