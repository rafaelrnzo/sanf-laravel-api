<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentSummaryEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public int $jumlah_tagihan;

    public float $total_tagihan;
}
