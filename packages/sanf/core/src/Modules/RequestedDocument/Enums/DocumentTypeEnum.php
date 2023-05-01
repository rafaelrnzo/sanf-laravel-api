<?php

namespace Sanf\Core\Modules\RequestedDocument\Enums;

use MyCLabs\Enum\Enum;

class DocumentTypeEnum extends Enum
{
    public const CONTRACT = 10;
    public const SUBMISSION = 20;
    public const PERSONAL = 30;

    public const CONTRACT_LABEL = 'KONTRAK';
    public const SUBMISSION_LABEL = 'PENGAJUAN';
    public const PERSONAL_LABEL = 'PERSONAL';
    public const COMPANY_LABEL = 'COMPANY';

    public const ALL = [
        self::CONTRACT,
        self::SUBMISSION,
        self::PERSONAL,
    ];
}
