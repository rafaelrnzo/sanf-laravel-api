<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class SanfCorePayInstallmentPayload extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $id_transaksi;
    public string $status_pembayaran;
    public string $tgl_pembayaran;
    public string $metode_bayar;
    public string $bank;
    public string $nomor_va;
    public ?string $biller_code;
    public ?string $bill_key;
    public int $total_bayar;
    public ?int $admin_fee;
    public ?int $nominal_kustom;
    public ?int $nominal_kustom_denda;
    /**
     * @var array|SanfCoreInstallmentPaymentItem[]
     */
    public array $detail_pembayaran;
}
