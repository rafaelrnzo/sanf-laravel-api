<?php

namespace Sanf\Core\Modules\Financing\Enums;

use MyCLabs\Enum\Enum;

class FinancingMethodEnum extends Enum
{
    public const PEMBELIAN_ANGSURAN = 1;
    public const SEWA_PEMBIAYAAN = 3;
    public const JUAL_SEWA_BALIK = 4;
    public const FASILITAS_MODAL_USAHA = 6;
    public const ANJAK_PIUTANG_PEMBERIAN = 7;
    public const ANJAK_PIUTANG_TANPA_PEMBERIAN = 8;
}
