<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SanfCorePayInstallmentPayload extends DataTransferObject
{
    public string $id_transaksi;
    public string $status_pembayaran;
    public string $tgl_pembayaran;
    public string $metode_bayar;
    public string $bank;
    public string $nomor_va;
    public int $total_bayar;
    /**
     * @var array|SanfCoreInstallmentPaymentItem[]
     */
    public array $detail_pembayaran;
}
