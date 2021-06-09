<?php

namespace NbsPhp\Core\Traits;

use Carbon\Carbon;

trait TimezoneMutable
{
    /**
     * @param $value
     * @param $format
     * @return Carbon|string
     */
    public function setTimezoneUtc($value, $format)
    {
        return Carbon::createFromFormat($format, $value, config('core.timezone_name'))
            ->utc()
            ->locale(config('core.timezone_locale'));
    }

    /**
     * @param $value
     * @return Carbon|string
     */
    public function getTimezoneLocale($value)
    {
        return Carbon::parse($value)
            ->timezone(config('core.timezone_name'))
            ->locale(config('core.timezone_locale'));
    }
}
