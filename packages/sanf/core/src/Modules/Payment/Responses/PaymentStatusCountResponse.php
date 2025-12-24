<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentStatusCountResponse extends DataTransferObject
{
    public string $status;
    public int $total;
}
