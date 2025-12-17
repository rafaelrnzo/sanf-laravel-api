<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SanfCoreSubmitSparePartFinancingPayload extends DataTransferObject
{
    public string $BATCH_ID;
    public string $SUPPLIER_ID;
    /** @var SanfCoreSubmitSparePartFinancingInvoicePayload[] */
    public array $INVOICE;
    public SanfCoreSubmitSparePartFinancingBankAccountPayload $BANK_ACCOUNT;
    /** @var SanfCoreSubmitSparePartFinancingDocumentPayload[] */
    public array $DOCUMENTS;
}
