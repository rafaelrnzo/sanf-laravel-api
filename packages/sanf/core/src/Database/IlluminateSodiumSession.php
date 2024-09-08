<?php

namespace Sanf\Core\Database;

use NbsPhp\Core\Database\TransactionalSessionInterface;
use Sanf\Core\Encryptions\SodiumEncryption;

class IlluminateSodiumSession implements TransactionalSessionInterface
{
    public function executeAtomically(callable $operation)
    {
        return SodiumEncryption::query()->transaction($operation);
    }
}
