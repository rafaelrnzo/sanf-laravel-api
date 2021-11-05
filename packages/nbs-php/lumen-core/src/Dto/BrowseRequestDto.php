<?php


namespace NbsPhp\Core\Dto;


class BrowseRequestDto extends CamelCaseDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
