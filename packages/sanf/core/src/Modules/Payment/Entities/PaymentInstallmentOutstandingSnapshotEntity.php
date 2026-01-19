<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\DataTransferObject;

final class PaymentInstallmentOutstandingSnapshotEntity extends DataTransferObject
{
    public ?string $due_date;
    public ?int $total;
    public ?int $principal_loan;
    public ?int $interest_amount;
    public ?int $penalty_fee;

    /** @deprecated change to interest_amount */
    public ?string $bunga;

    /** @deprecated change to penalty_fee */
    public ?string $denda;

    /** @deprecated */
    public ?string $diskon;

    /** @deprecated */
    public ?string $admin_fee;

    /** @deprecated change to due_date */
    public ?string $jatuh_tempo;

    /** @deprecated change to principal_loan */
    public ?string $pokok_hutang;

    /** @deprecated change to total */
    public ?string $total_overdue;

    /** @deprecated */
    public ?string $status_pembayaran_id;

    /** @deprecated */
    public ?string $status_pembayaran_desc;
}
