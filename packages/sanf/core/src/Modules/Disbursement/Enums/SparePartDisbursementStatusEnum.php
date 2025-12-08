<?php

namespace Sanf\Core\Modules\Disbursement\Enums;

use MyCLabs\Enum\Enum;

/**
 * Spare Part Disbursement Status Enum.
 */
class SparePartDisbursementStatusEnum extends Enum
{
    public const DRAFT = 10;
    public const WAITING_CUSTOMER = 20;
    public const NEED_REVIEW = 30;
    public const WAITING_VALIDATION = 40;
    public const WAITING_PAYMENT = 41;
    public const PAYMENT_COMPLETED = 50;
    public const REJECTED = 60;
    public const APPROVED = 70;
}
