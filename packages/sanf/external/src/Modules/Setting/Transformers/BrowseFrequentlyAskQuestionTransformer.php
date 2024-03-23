<?php

namespace Sanf\External\Modules\Setting\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class BrowseFrequentlyAskQuestionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'title' => Str::title($dto->title),
            'description' => $dto->description,
            'is_popular' => $dto->isPopular,
            'order' => $dto->order,
            'category_id' => $dto->categoryId,
            'category' => $dto->category,
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt),
        ];
    }
}
