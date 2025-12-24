<?php

namespace Sanf\Integration\Modules\Midtrans\Responses;

use Spatie\DataTransferObject\DataTransferObject;

class CreateSnapTransactionResponse extends DataTransferObject
{
    public string $token;
    public string $redirect_url;
}
