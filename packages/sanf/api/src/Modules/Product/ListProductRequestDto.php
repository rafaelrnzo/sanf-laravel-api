<?php


namespace Sanf\Api\Modules\Product;


use Spatie\DataTransferObject\DataTransferObject;

class ListProductRequestDto extends DataTransferObject
{
    public int $limit = 10;

    public int $offset = 0;

    public ?int $id;

    public ?string $title;
}