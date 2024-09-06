<?php

namespace Sanf\Core\Database;

interface MultipleTransactionalSessionInterface
{
    /**
     * @param callable $operation
     * @return mixed
     */
    public function executeAtomically(callable $operation);
}
