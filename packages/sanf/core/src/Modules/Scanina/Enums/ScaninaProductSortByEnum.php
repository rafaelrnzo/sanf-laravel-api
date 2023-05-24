<?php

namespace Sanf\Core\Modules\Scanina\Enums;

use MyCLabs\Enum\Enum;

class ScaninaProductSortByEnum extends Enum
{
    public const NEWEST = '11';
    public const OLDEST = '20';
    public const AZ = '12';
    public const ZA = '13';
    public const MIN_PRICE = '14';
    public const MAX_PRICE = '15';
    public const YEAR_ASC = '16';
    public const YEAR_DESC = '17';
    public const LOCATION_AZ = '18';
    public const LOCATION_ZA = '19';
    public const MIN_HOUR_METER = '1hmmin';
    public const MAX_HOUR_METER = '1hmmax';
    public const MIN_KILO_METER = '2kmmin';
    public const NEARBY = '21';

    public const ALL = [
        self::NEWEST,
        self::OLDEST,
        self::AZ,
        self::ZA,
        self::MIN_PRICE,
        self::MAX_PRICE,
        self::YEAR_ASC,
        self::YEAR_DESC,
        self::LOCATION_AZ,
        self::LOCATION_ZA,
        self::MIN_HOUR_METER,
        self::MAX_HOUR_METER,
        self::MIN_KILO_METER,
        self::NEARBY,
    ];
}
