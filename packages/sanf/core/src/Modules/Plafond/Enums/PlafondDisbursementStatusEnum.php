<?php

namespace Sanf\Core\Modules\Plafond\Enums;

use MyCLabs\Enum\Enum;

final class PlafondDisbursementStatusEnum extends Enum
{
    public const SUBMIT = 10;
    public const ON_PROCESS = 20;
    public const REVISION = 21;
    public const DONE = 30;
    public const APPROVE = 31;
    public const REJECT = 32;

    public const APPROVE_CORE = ['SELESAI', 'TELAH DICAIRKAN'];
    public const CORE_APPROVAL = 'DISETUJUI';
    public const CORE_REJECTED = 'DITOLAK';

    public const ALL_TAB = [
        self::SUBMIT,
        self::ON_PROCESS,
        self::DONE,
    ];

    public const SUBMIT_TAB = [
        self::SUBMIT,
    ];

    public const PROCESS_TAB = [
        self::ON_PROCESS,
        self::REVISION,
        self::DONE,
    ];

    public const DONE_TAB = [
        self::APPROVE,
        self::REJECT,
        self::APPROVE_CORE,
    ];

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return __('core::constant.plafond.disbursement.status.' . $this->getKey());
    }
}
