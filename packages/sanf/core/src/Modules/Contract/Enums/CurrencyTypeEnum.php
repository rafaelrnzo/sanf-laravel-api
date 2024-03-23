<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class CurrencyTypeEnum extends Enum
{
    const IDR = 'IDR';
    const USD = 'USD';
    const SYMBOL = [
        self::IDR => 'Rp.',
        self::USD => '$',
    ];

    public function getSymbol()
    {
        return self::SYMBOL[$this->getValue()];
    }
}
