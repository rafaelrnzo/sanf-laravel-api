<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

final class PaymentStatusLogItemEntity extends FlexibleDataTransferObject
{
    public ?string $status;

    /**
     * e.g. "2025-12-19T00:00:00+00:00".
     * @var string|null
     */
    public ?string $updated_at;
}
