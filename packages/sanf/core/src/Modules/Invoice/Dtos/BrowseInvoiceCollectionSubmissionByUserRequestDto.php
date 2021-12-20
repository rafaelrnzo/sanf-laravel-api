<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseInvoiceCollectionSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public ?int $statusId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $timestamp;
}
