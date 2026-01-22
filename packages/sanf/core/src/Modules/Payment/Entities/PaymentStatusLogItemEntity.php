<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\DataTransferObject;

final class PaymentStatusLogItemEntity extends DataTransferObject
{
    public ?string $status;

    /**
     * e.g. "2025-12-19T00:00:00+00:00".
     * @var string
     */
    public ?string $updated_at;
}
