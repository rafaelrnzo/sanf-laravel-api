<?php

namespace Sanf\Core\Modules\Location;

use MyCLabs\Enum\Enum;

/**
 * @method static $this|int COUNTRY_LV()
 * @method static $this|int PROVINCE_LV()
 * @method static $this|int CITY_LV()
 * @method static $this|int DISTRICT_LV()
 * @method static $this|int SUBDISTRICT_LV()
 */
class LocationEnum extends Enum
{
    const COUNTRY_LV = 1;
    const PROVINCE_LV = 2;
    const CITY_LV = 3;
    const DISTRICT_LV = 4;
    const SUBDISTRICT_LV = 5;
}
