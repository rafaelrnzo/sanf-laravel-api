<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowseFinancingUnitLocationSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public array $data;
    public PaginateResponseDto $paginate;
}
