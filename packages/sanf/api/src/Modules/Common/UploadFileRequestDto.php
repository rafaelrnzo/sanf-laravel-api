<?php


namespace Sanf\Api\Modules\Common;


use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileRequestDto extends DataTransferObject
{
    public UploadedFile $file;

    public int $type;
}