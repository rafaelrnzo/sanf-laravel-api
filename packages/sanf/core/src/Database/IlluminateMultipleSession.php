<?php

namespace Sanf\Core\Database;

use Sanf\Core\Encryptions\SodiumEncryption;

class IlluminateMultipleSession implements MultipleTransactionalSessionInterface
{
    public function executeAtomically(callable $operation)
    {
        $sodiumQuery = SodiumEncryption::query();

        $sodiumQuery->multipleBeginTransaction();

        try {
            $sodiumQuery->hideLogStatement();

            $result = $operation();

            $sodiumQuery->multipleCommit();

        } catch (\Throwable $th) {
            $sodiumQuery->multipleRollBack();

            throw $th;
        }

        return $result;
    }
}
