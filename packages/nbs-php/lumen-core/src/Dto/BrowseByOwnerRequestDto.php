<?php


namespace NbsPhp\Core\Dto;


class BrowseByOwnerRequestDto extends CamelCaseDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
