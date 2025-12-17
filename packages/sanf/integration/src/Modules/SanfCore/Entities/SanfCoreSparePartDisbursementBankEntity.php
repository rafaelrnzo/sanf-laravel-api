<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreSparePartDisbursementBankEntity extends FlexibleDataTransferObject
{
    public string $bank_id;
    public string $owner;
    public string $provider;
    public string $account_number;
}
