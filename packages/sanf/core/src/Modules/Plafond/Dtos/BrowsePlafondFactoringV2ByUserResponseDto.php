<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowsePlafondFactoringV2ByUserResponseDto extends CamelCaseDataTransferObject
{
    public $data;
    public PaginateResponseDto $paginate;
}
