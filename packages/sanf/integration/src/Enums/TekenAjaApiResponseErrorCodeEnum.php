<?php

namespace Sanf\Integration\Enums;

use MyCLabs\Enum\Enum;

class TekenAjaApiResponseErrorCodeEnum extends Enum
{
    public const SYSTEM_FAILURE = 'SYSTEM_FAILURE';

    /** Registration Code */
    public const EMAIL_EXISTS = 'EMAIL_EXISTS';
    public const USER_EXISTS = 'USER_EXISTS';
    public const SELFIE_UNMATCH = 'SELFIE_UNMATCH';
    public const BIODATA_UNMATCH = 'BIODATA_UNMATCH';
    public const INVALID_PARAMETER = 'INVALID_PARAMETER';

    /** Register Check or Send Verification Code */
    public const USER_EXISTS_VERIFIED = 'USER_EXISTS_VERIFIED';
    public const NIK_EMAIL_MATCHED = 'NIK_EMAIL_MATCH';
    public const USER_DO_NOT_EXISTS = 'USER_DO_NOT_EXISTS';
    public const USER_EXISTS_UNVERIFIED = 'USER_EXISTS_UNVERIFIED';
    public const USER_EXISTS_CERTIFICATE_EXPIRED = 'USER_EXISTS_CERTIFICATE_EXPIRED';
    public const NIK_EMAIL_UNMATCH = 'NIK_EMAIL_UNMATCH';

    /** Document Code */
    public const NOT_COMPLETE_SIGN = 'DOCUMENT_IS_NOT_COMPLETELY_SIGNED';
    public const NOT_FOUND = 'DOCUMENT_NOT_FOUND';
    public const ACCESS_UNAUTHORIZED = 'DOCUMENT_ACCESS_UNAUTHORIZED';
}
