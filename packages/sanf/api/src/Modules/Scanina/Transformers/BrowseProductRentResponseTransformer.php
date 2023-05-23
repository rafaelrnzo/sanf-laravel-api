<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductRentResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        $originPrice = (float)optional($dto)->priceBefore;
        $cutPrice = (float)optional($dto)->price;
        $discount = (($originPrice - $cutPrice) / $originPrice) * 100;
        $availability = [];

        if ($dto->monthPrice) {
            $availability[] = 'monthly';
        }

        if ($dto->dayPrice) {
            $availability[] = 'daily';
        }

        if ($dto->hourPrice) {
            $availability[] = 'hourly';
        }

        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
            'image_url' => (string)optional($dto)->imageFiles->path,
            'country' => (string)optional($dto)->country, //TODO get from scanina api
            'city' => (string)optional($dto)->locationName,
            'district' => (string)optional($dto)->district,
            'year' => (int)optional($dto)->year,
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'availability' => $availability
        ];
    }
}
