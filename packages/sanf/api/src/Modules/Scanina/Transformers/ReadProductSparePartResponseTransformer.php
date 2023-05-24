<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Spatie\Fractalistic\ArraySerializer;

class ReadProductSparePartResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var ReadProductSparePartResponseDto $dto */

        $originPrice = (float)optional($dto)->priceBefore;
        $cutPrice = (float)optional($dto)->price;
        $discount = (($originPrice - $cutPrice) / $originPrice) * 100;

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles);

        $length = (float)optional($dto)->length;
        $width = (float)optional($dto)->width;
        $height = (float)optional($dto)->height;

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
            "category" => (string)optional($dto)->category,
            "weight" => (float)optional($dto)->weight . "kg",
            "dimension" => "{$length}cm x {$width}cm x {$height}cm",
            'customer_reviews' => fractal($dto->customerReviews, BrowseProductCustomerReviewResponseTransformer::class)
                ->serializeWith(new ArraySerializer()),
        ];
    }
}
