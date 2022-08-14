<?php

namespace Sanf\Core\Modules\Faq\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ListFaqDto extends DataTransferObject
{
    public ?int $limit = null;
    public ?int $offset = null;
    public string $orderBy = 'order';
    public string $orderDirection = 'asc';
    public ?string $searchKeyword = null;
    public ?int $categoryId = null;
    public ?bool $isPopular = null;
}
