<?php

namespace Sanf\Core\Modules\Disbursement\Enums;

use MyCLabs\Enum\Enum;

/**
 * Spare Part Disbursement Batch Status Enum.
 */
class SparePartDisbursementBatchStatusEnum extends Enum
{
    public const DRAFT = 'draft';
    public const SUBMITTED = 'submitted';
}
