<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentDetailOverdueEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public string $due_date; // 'Y-m-d' format
    public float $pokok_hutang;
    public float $bunga;
    public float $denda;
    public float $total_overdue;
}
