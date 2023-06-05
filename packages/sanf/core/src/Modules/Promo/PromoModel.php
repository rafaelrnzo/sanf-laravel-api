<?php

namespace Sanf\Core\Modules\Promo;

use NbsPhp\Core\Models\AbstractModel;

class PromoModel extends AbstractModel
{
    public const SANF_SCANINA = 'sanf-scanina';

    protected $table = 'promo_sanf';
}
