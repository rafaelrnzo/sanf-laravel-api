<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\DataTransferObject;

final class PaymentUserSnapshotEntity extends DataTransferObject
{
    public int $id;
    public string $username;
    public string $full_name;
    public string $phone_number;
    public string $xid;
}
