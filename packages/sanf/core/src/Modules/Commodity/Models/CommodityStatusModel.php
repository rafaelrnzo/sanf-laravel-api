<?php

namespace Sanf\Core\Modules\Commodity\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;

class CommodityStatusModel extends AbstractModel
{
    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'commodity_status';
}
