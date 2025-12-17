<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SanfCoreSubmitSparePartFinancingInvoicePayload extends DataTransferObject
{
    public string $CUST_ID;
    public string $NO_INVOICE;
    public string $TANGGAL_INVOICE;
    public string $CURRENCY;
    public string $TOTAL_INVOICE;
    public string $STATUS_INVOICE_ID; // 01 | 02
    public string $STATUS_INVOICE_DESC; // 01 -> Disetujui | 02 -> Ditolak
}
