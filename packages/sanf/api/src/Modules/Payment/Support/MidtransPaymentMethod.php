<?php

namespace Sanf\Api\Modules\Payment\Support;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class MidtransPaymentMethod extends FlexibleDataTransferObject
{
    public ?string $type;
    public ?string $provider;
    public ?string $virtual_account_number;
    public ?string $biller_code;
    public ?string $bill_key;
    public ?string $provider_logo_url;

    public static function empty(): self
    {
        return new self();
    }
}
