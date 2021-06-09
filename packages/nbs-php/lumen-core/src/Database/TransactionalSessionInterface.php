<?php


namespace NbsPhp\Core\Database;


interface TransactionalSessionInterface
{
    /**
     * @param callable $operation
     * @return mixed
     */
    public function executeAtomically(callable $operation);
}
