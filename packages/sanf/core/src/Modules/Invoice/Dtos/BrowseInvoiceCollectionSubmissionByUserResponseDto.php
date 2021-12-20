<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;
use NbsPhp\Core\Dto\PaginateResponseDto;

class BrowseInvoiceCollectionSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public array $data;
    public PaginateResponseDto $paginate;
}
