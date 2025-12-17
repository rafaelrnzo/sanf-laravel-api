<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\PaginateResponseDto;
use Spatie\DataTransferObject\DataTransferObject;

class BrowsePlafondSparePartResponseDto extends DataTransferObject
{
    public $data;
    public PaginateResponseDto $paginate;
}
