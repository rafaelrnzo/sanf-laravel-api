<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreSparePartDisbursementDocumentEntity extends FlexibleDataTransferObject
{
    public string $doc_id;
    public string $file_path;
    public string $file_name;
}
