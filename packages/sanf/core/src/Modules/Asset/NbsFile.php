<?php

namespace Sanf\Core\Modules\Asset;

use Spatie\DataTransferObject\DataTransferObject;

class NbsFile extends DataTransferObject
{
    public string $originName;
    public string $path;
    public string $visibility;
    public string $mimeType;
    public int $size;
    public int $timestamp;
    public string $dirname;
    public string $basename;
    public string $extension;
    public string $filename;
}
