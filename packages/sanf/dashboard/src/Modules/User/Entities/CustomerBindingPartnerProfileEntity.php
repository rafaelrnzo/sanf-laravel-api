<?php

namespace Sanf\Dashboard\Modules\User\Entities;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CustomerBindingPartnerProfileEntity extends FlexibleDataTransferObject
{
    public ?string $cust_id_sanf;
    public ?string $id_identity;
    public ?string $desc_identity;
    public ?string $company_type;
    public ?string $identity_name;
    public ?string $no_telp;
    public ?string $tipe_supplier;
}