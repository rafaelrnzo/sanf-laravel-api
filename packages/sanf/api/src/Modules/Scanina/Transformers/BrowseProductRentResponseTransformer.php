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

        if ($dto->monthPrice && $dto->monthPrice > 0) {
            $availability[] = 'monthly';
            if ((float)optional($dto)->priceBefore <= 0) {
                $originPrice = (float)optional($dto)->monthPrice;
            }
            if ((float)optional($dto)->price <= 0) {
                $cutPrice = (float)optional($dto)->monthPrice;
            }
        }

        if ($dto->dayPrice && $dto->dayPrice > 0) {
            $availability[] = 'daily';
            if ((float)optional($dto)->priceBefore <= 0) {
                $originPrice = (float)optional($dto)->dayPrice;
            }
            if ((float)optional($dto)->price <= 0) {
                $cutPrice = (float)optional($dto)->dayPrice;
            }
        }

        if ($dto->hourPrice && $dto->hourPrice > 0) {
            $availability[] = 'hourly';
            if ((float)optional($dto)->priceBefore <= 0) {
                $originPrice = (float)optional($dto)->hourPrice;
            }
            if ((float)optional($dto)->price <= 0) {
                $cutPrice = (float)optional($dto)->hourPrice;
            }
        }

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles ?? []);

        $response = [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
            'image_url' => $imagesFiles[0] ?? null,
            'location' => (string)optional($dto)->location,
            'year' => (int)optional($dto)->year,
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'availability' => $availability,
            'stock' => (int)optional($dto)->stock,
        ];

        if (optional($dto)->startDate) {
            $response['start_rent_at'] = optional($dto)->startDate;
        }

        if (optional($dto)->endDate) {
            $response['end_rent_at'] = optional($dto)->endDate;
        }

        if (optional($dto)->quantity) {
            $response['quantity'] = optional($dto)->quantity;
        }

        return $response;
    }
}
