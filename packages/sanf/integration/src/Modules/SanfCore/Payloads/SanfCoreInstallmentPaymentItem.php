<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class SanfCoreInstallmentPaymentItem extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $no_kontrak;
    public string $cust_id;
    public string $due_date;
    public ?int $schedule_no;
    public int $amount_tagihan;
    public int $amount_pinalty;
    public int $total_pembayaran;
}
