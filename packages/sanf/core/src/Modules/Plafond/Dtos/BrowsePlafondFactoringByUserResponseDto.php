<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowsePlafondFactoringByUserResponseDto extends CamelCaseDataTransferObject
{
    public $data;
    public PaginateResponseDto $paginate;
}
