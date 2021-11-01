<?php


namespace Sanf\Core\Modules\Financing\Dto;

use Illuminate\Http\UploadedFile;
use Spatie\DataTransferObject\DataTransferObject;

class UploadFinancingDocumentDto extends DataTransferObject
{
    public string $xid;

    public UploadedFile $file;

    public string $asset_type;

    public $author = 'SANF Mobile';
}
