<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;

class PlafondTypeModel extends AbstractModel
{
    protected $table = 'plafond_type';

    protected $casts = ['id' => 'string'];
}
