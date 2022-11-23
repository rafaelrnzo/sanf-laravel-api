<?php

namespace Sanf\Core\Modules\Financing\Enums;

use MyCLabs\Enum\Enum;

class FinancingStatusEnum extends Enum
{
    public const PROCESSED = 10;
    public const ACCEPTED = 20;
    public const REJECTED = 30;

    public const CORE_PROCESSED = 'Diproses';
    public const CORE_ACCEPTED = 'Disetujui';
    public const CORE_REJECTED = 'Ditolak';

    public const STATUS_ID = [
        'Diproses' => self::PROCESSED,
        'Disetujui' => self::ACCEPTED,
        'Ditolak' => self::REJECTED,
    ];

    public function getStatusId()
    {
        return self::STATUS_ID[$this->value];
    }
}
