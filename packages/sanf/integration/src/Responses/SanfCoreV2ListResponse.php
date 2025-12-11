<?php

namespace Sanf\Integration\Responses;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreV2ListResponse extends FlexibleDataTransferObject
{
    public ?string $status;
    public ?string $message;
    public array $data;
    public SanfCoreV2MetaResponse $meta;
}