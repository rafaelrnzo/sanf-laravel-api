<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SanfCoreSubmitSparePartFinancingBankAccountPayload extends DataTransferObject
{
    public string $BANK_ID;
    public string $OWNER;
    public string $PROVIDER;
    public string $ACCOUNT_NUMBER;
}
