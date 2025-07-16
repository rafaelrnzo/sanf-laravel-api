<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddInvoiceCollectionSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;

    /**
     * @since CR2025 Changed to nullable
     */
    public ?\DateTimeImmutable $pickupDate;
    public array $financingUnits;
}
