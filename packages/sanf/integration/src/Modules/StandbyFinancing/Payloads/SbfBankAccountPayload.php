<?php

namespace Sanf\Integration\Modules\StandbyFinancing\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SbfBankAccountPayload extends DataTransferObject
{
    public string $bank_id;
    public string $owner;
    public string $provider;
    public string $account_number;
    public string $total_amount;
}
