<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyResponseDto;

class BrowseProductBuyResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var BrowseProductBuyResponseDto $dto */
        $originPrice = (float) optional($dto)->priceBefore;
        $cutPrice = (float) optional($dto)->price;
        $discount = (($originPrice - $cutPrice) > 0) ? (($originPrice - $cutPrice) / $originPrice) * 100 : 0;

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles ?? []);

        $response = [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string) optional($dto)->name,
            'image_url' => $imagesFiles[0] ?? null,
            'location' => (string) optional($dto)->location,
            'year' => (int) optional($dto)->year,
            'engine_used' => (object) [
                'amount' => (float) optional($dto->unitMeasurement)->rate,
                'unit' => (string) optional($dto->unitMeasurement)->measurement,
            ],
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'stock' => (int) optional($dto)->stock,
        ];

        if (optional($dto)->quantity) {
            $response['quantity'] = optional($dto)->quantity;
        }

        return $response;
    }
}
