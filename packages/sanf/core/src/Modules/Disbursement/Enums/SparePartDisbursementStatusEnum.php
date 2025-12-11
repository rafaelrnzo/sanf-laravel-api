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
    public const CANCELED = 80;

    public static function disbursementStatuses()
    {
        return [
            self::DRAFT,
            self::WAITING_CUSTOMER,
            self::NEED_REVIEW,
            self::WAITING_VALIDATION,
            self::PAYMENT_COMPLETED,
            self::REJECTED,
            self::CANCELED,
        ];
    }

    public static function disbursementInvoiceStatuses()
    {
        return [
            self::WAITING_CUSTOMER,
            self::WAITING_PAYMENT,
            self::PAYMENT_COMPLETED,
            self::APPROVED,
            self::REJECTED,
        ];
    }

    public function getLabel()
    {
        return __('core::constant.spare_part_disbursement.status.' . $this->getKey());
    }
}
