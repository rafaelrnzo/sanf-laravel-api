<?php

namespace Sanf\Core\Modules\Ocr\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ExtractOcrRequestDto extends DataTransferObject
{
    public string $userId;
    public string $profileXid;
    public string $mode;
    /** @var \Illuminate\Http\UploadedFile[] */
    public array $files;
    public ?string $llmApiKey;
}
