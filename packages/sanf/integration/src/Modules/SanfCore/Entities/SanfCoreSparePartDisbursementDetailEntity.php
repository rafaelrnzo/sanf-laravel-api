<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreSparePartDisbursementDetailEntity extends FlexibleDataTransferObject
{
    public string $batch_id;
    public string $cust_id;
    public string $cust_name;
    public string $tipe_pembayaran_id;
    public string $tipe_pembayaran_desc;
    public string $tanggal_pengajuan;
    public string $status_batch_id;
    public string $status_batch_desc;
    public array $invoices;
    public SanfCoreSparePartDisbursementBankEntity $bank;

    /** @var \Sanf\Integration\Modules\SanfCore\Entities\SanfCoreSparePartDisbursementDocumentEntity[] */
    public array $documents;
}
