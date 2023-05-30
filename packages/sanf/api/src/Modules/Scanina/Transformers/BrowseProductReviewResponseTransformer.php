<?php

namespace Sanf\Api\Modules\Scanina\Transformers;

use League\Fractal\TransformerAbstract;

class BrowseProductReviewResponseTransformer extends TransformerAbstract
{
    public function transform($dto): array
    {
        return [
            'name' => (string)optional($dto->userSnapshot)->fullName,
            'rating' => (float)optional($dto)->rating,
            'good_review' => (array)optional($dto)->statisfiedWith,
            'bad_review' => (array)optional($dto)->unsatisfiedWith,
            'comment' => (string)optional($dto)->comment,
            'images_url' => [optional($dto->images)->path],
        ];
    }
}
