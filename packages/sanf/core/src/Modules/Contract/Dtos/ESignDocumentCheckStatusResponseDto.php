<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ESignDocumentCheckStatusResponseDto extends DataTransferObject
{
    public string $document_id;
    public int $previous_status_id;
    public int $current_status_id;
    public bool $checked;

    /**
     * Timestamp format.
     * @var int|null
     */
    public ?int $retry_available_at;
}
