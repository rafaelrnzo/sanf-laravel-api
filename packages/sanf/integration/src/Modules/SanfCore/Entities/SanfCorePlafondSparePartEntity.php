<?php

namespace Sanf\Integration\Modules\SanfCore\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCorePlafondSparePartEntity extends FlexibleDataTransferObject
{
    public ?string $no_plafond;
    public ?string $cust_id;
    public ?string $p_code;
    public $p_terpakai;
    public ?string $p_sisa;
    public ?string $exp_date;
    public ?string $nama_supplier;
    public ?string $tipe_supplier;
    public ?string $p_total;
    public ?string $p_invoice_ongoing;
}
