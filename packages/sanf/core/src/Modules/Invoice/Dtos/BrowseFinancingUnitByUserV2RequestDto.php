<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

/**
 * @since CR2025
 */
class BrowseFinancingUnitByUserV2RequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
