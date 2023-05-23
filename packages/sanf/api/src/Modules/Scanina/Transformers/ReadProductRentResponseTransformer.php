<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ReadProductRentResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        $originPrice = (float)optional($dto)->priceBefore;
        $cutPrice = (float)optional($dto)->price;
        $discount = (($originPrice - $cutPrice) / $originPrice) * 100;

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles);

        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
            'description' => (string)optional($dto)->description,
            'images_url' => (array)$imagesFiles,
            'country' => (string)optional($dto)->country, //TODO get from scanina api
            'city' => (string)optional($dto)->locationName,
            'district' => (string)optional($dto)->district,
            'year' => (int)optional($dto)->year,
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'provider' => (string)optional($dto)->provider,
            'serial_number' => (string)optional($dto)->serialNumber,
            'item_number' => (string)optional($dto)->itemNumber,
            'properties' => fractal($dto->technicalDetail, BrowseProductRentPropertiesResponseTransformer::class)
                ->serializeWith(new ArraySerializer()),
            'monthly_rate_amount' => (float)optional($dto)->monthPrice,
            'daily_rate_amount' => (float)optional($dto)->dayPrice,
            'hourly_rate_amount' => (int)optional($dto)->hourPrice,
            'start_available_at' => (int)optional($dto)->startDateAvailable,
            'end_available_at' => (float)optional($dto)->endDateAvailable,
            'specifications' => fractal($dto->specifications, BrowseProductSpecificationResponseTransformer::class)
                ->serializeWith(new ArraySerializer()),
        ];
    }
}
