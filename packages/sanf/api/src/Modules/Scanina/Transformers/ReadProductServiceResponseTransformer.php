<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Spatie\Fractalistic\ArraySerializer;

class ReadProductServiceResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var ReadProductServiceResponseDto $dto */

        $originPrice = (float)optional($dto)->priceBefore;
        $cutPrice = (float)optional($dto)->price;
        $discount = (($originPrice - $cutPrice) > 0) ? (($originPrice - $cutPrice) / $originPrice) * 100 : 0;

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles ?? []);

        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
            'description' => (string)optional($dto)->description,
            'images_url' => (array)$imagesFiles,
            'location' => (string)optional($dto)->location,
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
        ];
    }
}
