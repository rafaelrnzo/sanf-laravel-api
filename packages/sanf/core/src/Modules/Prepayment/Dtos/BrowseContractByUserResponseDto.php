<?php

namespace Sanf\Core\Modules\Prepayment\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowseContractByUserResponseDto extends CamelCaseDataTransferObject
{
    public array $data;
    public PaginateResponseDto $paginate;
}
