<?php

namespace Sanf\External\Modules\Notification\Enums;

use MyCLabs\Enum\Enum;

class PostSanfindUserTypeEnum extends Enum
{
    public const INSTALLMENT_OVERDUE = 'installment_overdue';
    public const INSTALLMENT_DUE_TODAY = 'installment_due_today';
    public const INSTALLMENT_ALMOST_DUE = 'installment_almost_due';
    public const CONTRACT_PUBLISHED = 'contract_published';
    public const BILL_TO_INSTALLMENT = 'bill_to_installment';
}
