<?php

namespace Sanf\Core\Modules\Financing;

use MyCLabs\Enum\Enum;

/**
 * @method static int ID_KTP()
 * @method static int ID_NPWP()
 * @method static string KTP()
 * @method static string NPWP()
 */
class FinancingDocumentAssetEnum extends Enum
{
    public const ID_KTP = 2;
    public const ID_NPWP = 3;

    public const KTP = 'KTP BORROWER';
    public const NPWP = 'NPWP';
}