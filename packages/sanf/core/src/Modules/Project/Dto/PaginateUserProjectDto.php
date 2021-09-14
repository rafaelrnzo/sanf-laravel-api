<?php


namespace Sanf\Core\Modules\Project\Dto;


use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PaginateUserProjectDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
