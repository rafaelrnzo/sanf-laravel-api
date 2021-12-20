<?php

namespace Sanf\Core\Modules\Insurance\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowseFinancingUnitByUserResponseDto extends CamelCaseDataTransferObject
{
    public array $data;
    public PaginateResponseDto $paginate;
}
