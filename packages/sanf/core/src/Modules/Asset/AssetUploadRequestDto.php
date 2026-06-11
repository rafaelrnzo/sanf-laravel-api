<?php

namespace Sanf\Core\Modules\Asset;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetUploadRequestDto extends DataTransferObject
{
    public UploadedFile $file;

    public int $type;

    /** @var string[]|null */
    public ?array $burn_text = null;
}
