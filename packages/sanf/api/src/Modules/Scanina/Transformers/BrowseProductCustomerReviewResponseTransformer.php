<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductCustomerReviewResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        $imagesFiles = array_map(function ($files) {
            return $files->path;
        }, $dto->images);

        return [
            'name' => (string)optional($dto)->userSnapshot->fullName,
            'rating' => (string)optional($dto)->rating,
            'good_review' => (array)optional($dto)->statisfiedWith,
            'bad_review' => (array)optional($dto)->unsatisfiedWith,
            'comment' => (string)optional($dto)->comment,
            'images_url' => $imagesFiles,
        ];
    }
}
