<?php


namespace Sanf\Api\Modules\Common;


use Spatie\DataTransferObject\DataTransferObject;

class UploadFileResultDto extends DataTransferObject
{
    public string $origin_name;

    public string $file_name;

    public string $url;
}