<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentDetailEStatementEntity extends FlexibleDataTransferObject
{
    public ?string $file_path; // e.g. "e_statement/"
    public ?string $file_name; // e.g. "sample.pdf"
}
