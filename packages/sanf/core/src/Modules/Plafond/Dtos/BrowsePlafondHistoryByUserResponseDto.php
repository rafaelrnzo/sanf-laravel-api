<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowsePlafondHistoryByUserResponseDto extends CamelCaseDataTransferObject
{
    public $data;
    public PaginateResponseDto $paginate;
}
