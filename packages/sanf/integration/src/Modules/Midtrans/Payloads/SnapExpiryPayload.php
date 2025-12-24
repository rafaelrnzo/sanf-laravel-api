<?php

namespace Sanf\Integration\Modules\Midtrans\Payloads;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SnapExpiryPayload extends FlexibleDataTransferObject
{
    /**
     * e.g. "2018-12-13 18:11:08 +0700".
     * @var string
     */
    public ?string $start_time;
    public ?string $unit;
    public ?int $duration;
}
