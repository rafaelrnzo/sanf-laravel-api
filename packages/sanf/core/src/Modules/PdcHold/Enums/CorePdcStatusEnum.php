<?php

namespace Sanf\Core\Modules\PdcHold\Enums;

use MyCLabs\Enum\Enum;

/**
 * From SANF Core.
 *
 * @since CR2025
 */
final class CorePdcStatusEnum extends Enum
{
    const DITERIMA = 1;
    const DIJALANKAN = 2;
    const DIHOLD = 3;
    const DIBATALKAN = 4;
    const DITOLAK = 5;
    const CAIR = 6;
    const DIKEMBALIKAN = 7;
    const PENDING = 8;
}
