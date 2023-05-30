<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentResponseDto;

class BrowseProductRentResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var BrowseProductRentResponseDto $dto */

        $originPrice = (float)optional($dto)->priceBefore;
        $cutPrice = (float)optional($dto)->price;
        $discount = (($originPrice - $cutPrice) > 0) ? (($originPrice - $cutPrice) / $originPrice) * 100 : 0;
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

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles ?? []);

        $response = [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
            'image_url' => $imagesFiles[0] ?? null,
            'country' => (string)optional($dto)->country, //TODO get from scanina api
            'city' => (string)optional($dto)->locationName,
            'district' => (string)optional($dto)->district,
            'year' => (int)optional($dto)->year,
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'availability' => $availability
        ];

        if (optional($dto)->startDate) {
            $response['start_rent_at'] = optional($dto)->startDate;
        }

        if (optional($dto)->endDate) {
            $response['end_rent_at'] = optional($dto)->endDate;
        }

        return $response;
    }
}
