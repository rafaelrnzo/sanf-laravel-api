<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreInstallmentDetailContractEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public string $no_kontrak;
    public ?string $status_kontrak_id;
    public ?string $status_kontrak_desc;
    public string $tanggal_kontrak;
    public ?string $nama_supplier;
    public ?string $tipe_pembayaran_id;
    public ?string $tipe_pembayaran_desc;
    public ?string $jenis_pembiayaan_id;
    public ?string $jenis_pembiayaan_desc;
    public ?string $cara_pembiayaan_id;
    public ?string $cara_pembiayaan_desc;
    public string $tenor;
    public string $tipe_tenor;
    public string $jatuh_tempo; // 'Y-m-d' format ": "2025-12-21",
    public ?string $tgl_selesai; // 'Y-m-d' format": null,
    public float $bunga_harian;
    public float $total_pembiayaan;
    public int $schedule_no; // angsuran ke ...
    public int $schedule_total; // dari ... angsuran
    public float $af_amount; // Nilai pembiayaan
    public float $dp_amount; // DP dibayarkan
    public float $ar_paid; // Nominal sudah dibayarkan
    public float $ar_outs; // Nominal sisa
}
