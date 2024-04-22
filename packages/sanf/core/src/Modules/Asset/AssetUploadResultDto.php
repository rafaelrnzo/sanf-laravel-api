<?php

namespace Sanf\Core\Modules\Asset;

use Spatie\DataTransferObject\DataTransferObject;

class AssetUploadResultDto extends DataTransferObject
{
    public string $path;

    public string $originName;

    public string $fileName;

    public string $url;
}
