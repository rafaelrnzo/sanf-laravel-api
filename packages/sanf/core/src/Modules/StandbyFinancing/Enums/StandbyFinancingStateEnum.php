<?php

namespace Sanf\Core\Modules\StandbyFinancing\Enums;

final class StandbyFinancingStateEnum
{
    public const SUBMITTED = 'SUBMITTED';
    public const STATE_CODE_SUBMITTED = '0';
    public const INVOICE_STATUS_SUBMITTED = 'submitted';
    public const LOCK_STATUS_LOCKED = 'LOCKED';

    public const ACTIVE_INVOICE_STATUSES = [
        'draft',
        self::INVOICE_STATUS_SUBMITTED,
        'pending_review',
        'approved',
        'disbursed',
    ];
}
