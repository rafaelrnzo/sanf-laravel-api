<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

class ReadProductServiceResponseTransformer extends TransformerAbstract
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
            'customer_reviews' => fractal($dto->customerReviews, BrowseProductCustomerReviewResponseTransformer::class)
                ->serializeWith(new ArraySerializer()),
        ];
    }
}
