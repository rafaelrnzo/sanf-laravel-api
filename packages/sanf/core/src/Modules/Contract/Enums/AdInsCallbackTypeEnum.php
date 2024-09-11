<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class AdInsCallbackTypeEnum extends Enum
{
    public const ACTIVATION_COMPLETE = 'ACTIVATION_COMPLETE';
    public const SIGNING_COMPLETE = 'SIGNING_COMPLETE';
    public const DOCUMENT_SIGN_COMPLETE = 'DOCUMENT_SIGN_COMPLETE';
    public const ALL_DOCUMENT_SIGN_COMPLETE = 'ALL_DOCUMENT_SIGN_COMPLETE';
}
