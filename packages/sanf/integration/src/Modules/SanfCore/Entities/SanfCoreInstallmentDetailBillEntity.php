<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentDetailBillEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public float $pokok_hutang;
    public float $bunga;
    public float $denda;
    public float $diskon;
    public float $admin_fee;
    public float $total_tagihan;
    public string $jatuh_tempo; // 'Y-m-d' format
    public int $status_pembayaran_id; // 0 | 1
    public string $status_pembayaran_desc; // "Belum Lunas" | "Lunas"
}
