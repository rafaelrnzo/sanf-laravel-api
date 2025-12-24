<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentDetailOverdueEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public string $due_date; // 'Y-m-d' format
    public int $pokok_hutang;
    public int $bunga;
    public int $denda;
    public int $total_overdue;
}
