<?php

namespace Sanf\Integration\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreSparePartDisbursementEntity extends FlexibleDataTransferObject
{
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
