<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreSparePartDisbursementEntity extends FlexibleDataTransferObject
{
    use CastsNumericDtoProperties;

    public string $rn;
    public string $batch_id;
    public string $cust_id;
    public string $cust_name;
    public int $total_invoice;
    public float $total_amount;
    public string $status_batch_id;
    public string $status_batch_desc;
    public string $tipe_pembayaran_id;
    public string $tipe_pembayaran_desc;
}
