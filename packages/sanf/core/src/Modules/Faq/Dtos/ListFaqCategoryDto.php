<?php

namespace Sanf\Core\Modules\Faq\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ListFaqCategoryDto extends DataTransferObject
{
    public ?int $limit = null;
    public ?int $offset = null;
    public string $orderBy = 'name';
    public string $orderDirection = 'asc';
    public ?int $id = null;
    public ?string $searchFaqKeyword = null;
}
