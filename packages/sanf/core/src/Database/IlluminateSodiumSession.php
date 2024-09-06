<?php

namespace Sanf\Core\Database;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use Sanf\Core\Constants\ConnectionDB;

class IlluminateSodiumSession implements TransactionalSessionInterface
{
    public function executeAtomically(callable $operation)
    {
        return DB::connection(ConnectionDB::PG_SODIUM)->transaction($operation);
    }
}
