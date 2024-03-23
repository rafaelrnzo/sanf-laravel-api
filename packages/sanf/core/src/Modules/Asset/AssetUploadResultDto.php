<?php

namespace Sanf\Core\Modules\Asset;

use Spatie\DataTransferObject\DataTransferObject;

class AssetUploadResultDto extends DataTransferObject
{
    public string $path;

    public string $origin_name;

    public string $file_name;

    public string $url;
}
