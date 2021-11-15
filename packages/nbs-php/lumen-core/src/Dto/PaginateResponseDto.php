<?php


namespace NbsPhp\Core\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class PaginateResponseDto extends DataTransferObject
{
    public int $total;
    public int $count;
    public int $skip;
    public int $limit;
    public string $sortBy = 'earliest';
}
