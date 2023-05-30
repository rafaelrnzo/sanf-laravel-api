<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductServiceResponseDto;

class BrowseProductServiceResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        /** @var BrowseProductServiceResponseDto $dto */

        $originPrice = (float)optional($dto)->priceBefore;
        $cutPrice = (float)optional($dto)->price;
        $discount = (($originPrice - $cutPrice) > 0) ? (($originPrice - $cutPrice) / $originPrice) * 100 : 0;

        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->imageFiles);

        return [
            'xid' => $dto->xid ?? $dto->id,
            'name' => (string)optional($dto)->name,
            'image_url' => $imagesFiles[0] ?: null,
            'country' => (string)optional($dto)->country, //TODO get from scanina api
            'city' => (string)optional($dto)->locationName,
            'district' => (string)optional($dto)->district,
            'price' => $originPrice,
            'discount' => $discount,
            'price_cut' => $cutPrice,
            'rating' => (float)optional($dto)->rating,
            'total_review' => (int)optional($dto)->reviewCount,
            'sold' => (int)optional($dto)->sold,
        ];

        if (optional($dto)->servicedAt || optional($dto)->notes) {
            $response['start_service_at'] = optional($dto)->servicedAt;
            $response['notes'] = optional($dto)->notes;
        }

        return $response;
    }
}
