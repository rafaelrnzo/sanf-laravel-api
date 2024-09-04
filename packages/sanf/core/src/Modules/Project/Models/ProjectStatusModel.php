<?php

namespace Sanf\Core\Modules\Project\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;

class ProjectStatusModel extends AbstractModel
{
    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'project_status';
}
