<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class PlafondDisbursementFormRequest extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $clientId;
    public string $plafondId;
    public DisbursementBowheerFormRequest $bouwheer;
    public $invoices;
    public float $totalInvoiceAmount;
    public $allocations;
    public DisbursementDocumentFormRequest $paymentAccDocument;
    public $otherDocument;
    public bool $customerReview;
    public int $createdAt;
}
