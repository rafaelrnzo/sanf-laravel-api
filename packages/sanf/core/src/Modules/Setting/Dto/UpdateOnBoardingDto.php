<?php

namespace Sanf\Core\Modules\Setting\Dto;

use Illuminate\Http\UploadedFile;
use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class UpdateOnBoardingDto extends CamelCaseDataTransferObject
{
    public string $xid;
    public ?string $title;
    public ?string $description;
    public ?UploadedFile $imageFile;
}
