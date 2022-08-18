<?php

namespace Sanf\Core\Modules\Setting\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ListFrequentlyAskQuestionCategoryPageDto extends DataTransferObject
{
    public ?string $keyword;
    public ?int $limit;
    public ?int $skip;
    public ?string $sortBy;
    public ?int $xid;
}
