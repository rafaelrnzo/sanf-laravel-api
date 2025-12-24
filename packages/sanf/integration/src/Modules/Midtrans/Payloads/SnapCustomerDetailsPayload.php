<?php

namespace Sanf\Integration\Modules\Midtrans\Payloads;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SnapCustomerDetailsPayload extends FlexibleDataTransferObject
{
    public ?string $first_name;
    public ?string $last_name;
    public ?string $email;
    public ?string $phone;
}
