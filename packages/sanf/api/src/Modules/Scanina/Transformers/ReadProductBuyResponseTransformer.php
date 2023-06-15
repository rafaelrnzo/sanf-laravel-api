<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Spatie\Fractalistic\ArraySerializer;

class ReadProductBuyResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var ReadProductBuyResponseDto $dto */

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
            'year' => (int)optional($dto)->year,
            'engine_used' => (object)[
                'amount' => (float)optional($dto->unitMeasurement)->rate,
                'unit' => (string)optional($dto->unitMeasurement)->measurement,
            ],
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'provider' => (string)optional($dto)->merchantName,
            'serial_number' => (string)optional($dto)->serialNumber,
            'item_number' => (string)optional($dto)->itemNumber,
            'specifications' => fractal($dto->subSpecifications, BrowseProductSubSpecificationResponseTransformer::class)
                ->serializeWith(new ArraySerializer()),
            'stock' => (int)optional($dto)->stock,
        ];
    }
}
