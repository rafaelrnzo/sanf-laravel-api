<?php

namespace Sanf\Core\Modules\Log\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class CreateWebhookLogPayload extends DataTransferObject
{
    public string $xid;
    public string $key;
    public string $reference_id;
    public array $payload;
    public string $received_at;
    public string $processed_at;
}
