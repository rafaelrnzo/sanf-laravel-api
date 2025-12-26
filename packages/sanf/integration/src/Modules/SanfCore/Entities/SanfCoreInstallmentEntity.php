<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public ?string $no_kontrak;
    public ?string $tipe_pembayaran_id;
    public ?string $tipe_pembayaran_desc;
    public ?float $total_tagihan;
    public ?string $jatuh_tempo; // 2025-12-21
    public ?string $supplier_id;
    public ?string $nama_supplier;
    public ?int $status_pembayaran_id; // 0 | 1
    public ?string $status_pembayaran_desc; // Lunas | Belum Lunas
    public ?int $schedule_no; // Lunas | Belum Lunas
    public ?int $schedule_total; // Lunas | Belum Lunas
}
