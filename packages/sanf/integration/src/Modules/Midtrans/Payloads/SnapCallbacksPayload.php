<?php

namespace Sanf\Integration\Modules\Midtrans\Payloads;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SnapCallbacksPayload extends FlexibleDataTransferObject
{
    public ?string $finish;
}
