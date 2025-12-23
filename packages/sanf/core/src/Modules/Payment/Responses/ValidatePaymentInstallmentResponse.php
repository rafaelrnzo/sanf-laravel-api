<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Spatie\DataTransferObject\DataTransferObject;

class ValidatePaymentInstallmentResponse extends DataTransferObject
{
    public array $validInstallments;

    public array $invalidInstallments;
    public array $unexistsInstallments;
}
