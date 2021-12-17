<?php

namespace Sanf\Core\Modules\Asset;

use MyCLabs\Enum\Enum;

/**
 * @method static int ID_AVATAR()
 * @method static int ID_KTP()
 * @method static int ID_NPWP()
 * @method static string AVATAR()
 * @method static string KTP()
 * @method static string NPWP()
 */
class AssetTypeEnum extends Enum
{
    public const AVATAR = 1;
    public const ID_KTP = 2;
    public const ID_NPWP = 3;
    public const INSURANCE_CLAIM = 3;

    public const KTP = 'KTP BORROWER';
    public const NPWP = 'NPWP';

    public const ASSET_TYPE = [self::AVATAR, self::ID_KTP, self::ID_NPWP, self::INSURANCE_CLAIM];
    public const PROFILE_DOCUMENT = [self::KTP, self::NPWP];
}
