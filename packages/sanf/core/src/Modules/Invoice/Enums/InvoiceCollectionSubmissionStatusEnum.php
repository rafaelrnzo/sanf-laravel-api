<?php

namespace Sanf\Core\Modules\Invoice\Enums;


use MyCLabs\Enum\Enum;

class InvoiceCollectionSubmissionStatusEnum extends Enum
{
    const PROCESSED = 10;
    const ACCEPTED = 20;
    const REJECTED = 30;
    const ALL_STATUS = [self::PROCESSED, self::ACCEPTED, self::REJECTED];

    public function getTranslation()
    {
        return __('core::constant.invoice_collection_submission.' . $this->getKey());
    }
}
