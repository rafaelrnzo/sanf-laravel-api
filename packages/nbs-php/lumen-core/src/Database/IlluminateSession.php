<?php

namespace NbsPhp\Core\Database;

use Illuminate\Support\Facades\DB;

class IlluminateSession implements TransactionalSessionInterface
{
    public function executeAtomically(callable $operation)
    {
        return DB::transaction($operation);
    }
}
