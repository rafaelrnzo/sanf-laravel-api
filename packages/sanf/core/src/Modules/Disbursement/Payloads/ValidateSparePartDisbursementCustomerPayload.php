<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class ValidateSparePartDisbursementCustomerPayload extends DataTransferObject
{
    /**
     * @var string|int
     */
    public $cust_id;

    public string $cust_id_sanfind;

    public string $tipe_pembayaran_id;
    public string $tipe_pembayaran_desc;
    public string $no_plafond;
    public string $status_code;
    public string $status_message;
    public ValidateSparePartDisbursementCustomerSummaryPayload $summary;

    /**
     * @var \Sanf\Core\Modules\Disbursement\Payloads\ValidateSparePartDisbursementCustomerInvoicePayload[]
     */
    public array $invoices;
}
