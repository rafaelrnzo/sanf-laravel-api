<?php

namespace Sanf\Core\Modules\Setting\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ListFrequentlyAskQuestionPageDto extends DataTransferObject
{
    public ?string $keyword;
    public ?int $limit;
    public ?int $skip;
    public ?string $sortBy;
    public ?bool $isPopular;
    public ?int $categoryId;
}
