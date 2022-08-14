<?php

namespace Sanf\External\Modules\Setting\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class BrowseFrequentlyAskQuestionCategoryTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => Str::title($dto->name),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt),
        ];
    }
}