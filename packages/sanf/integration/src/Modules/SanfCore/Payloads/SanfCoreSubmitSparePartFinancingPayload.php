<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SanfCoreSubmitSparePartFinancingPayload extends DataTransferObject
{
    public string $BATCH_ID;
    public string $SUPPLIER_ID;
    /** @var array|SanfCoreSubmitSparePartFinancingInvoicePayload[] */
    public array $INVOICE;
    public SanfCoreSubmitSparePartFinancingBankAccountPayload $BANK_ACCOUNT;
    /** @var array|SanfCoreSubmitSparePartFinancingDocumentPayload[] */
    public array $DOCUMENTS;
    public string $SUBMIT_TYPE;
}
