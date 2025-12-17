<?php

namespace Sanf\Integration\Responses;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreV2Response extends FlexibleDataTransferObject
{
    public ?string $status;
    public ?string $message;
    public $data;
}
