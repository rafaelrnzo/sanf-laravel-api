<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class PlafondDisbursementCoreFormRequest extends FlexibleDataTransferObject
{
    public string $cust_id;
    public string $p_code;
    public string $disbursement_no;
    public string $plafond_id;
    public string $bouwheer;
    public string $bouwheer_code;
    public float $amount;
    public array $invoices;
    public array $allocations;
    public ?array $percepatan_doc;
    public ?array $invoice_doc;
    public array $pendukung_doc;
    public int $created_at;
}
