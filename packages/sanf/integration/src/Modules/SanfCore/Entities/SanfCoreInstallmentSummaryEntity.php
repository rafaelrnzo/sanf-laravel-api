<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentSummaryEntity extends FlexibleDataTransferObject
{
    /**
     * @var float|int
     */
    public $jumlah_tagihan;

    /**
     * @var float|int
     */
    public $total_tagihan;
}
