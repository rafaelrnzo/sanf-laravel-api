<?php

namespace Sanf\Core\Modules\Ocr\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ExtractOcrResponseDto extends DataTransferObject
{
    public string $mode;
    public array $results;
}
