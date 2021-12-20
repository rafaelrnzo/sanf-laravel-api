<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddInvoiceCollectionSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public array $submissions;
}
