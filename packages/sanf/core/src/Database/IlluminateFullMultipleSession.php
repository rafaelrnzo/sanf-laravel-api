<?php

namespace Sanf\Core\Database;

use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Encryptions\SodiumEncryption;

class IlluminateFullMultipleSession implements MultipleTransactionalSessionInterface
{
    public function executeAtomically(callable $operation)
    {
        foreach ($this->getConnections() as $connection) {
            DB::connection($connection)->beginTransaction();
        }

        try {
            $this->hideLogStatement();

            $result = $operation();

            foreach ($this->getConnections() as $connection) {
                DB::connection($connection)->commit();
            }

        } catch (\Throwable $th) {
            foreach ($this->getConnections() as $connection) {
                DB::connection($connection)->rollBack();
            }

            throw $th;
        }

        return $result;
    }

    protected function getConnections(): array
    {
        return [
            ConnectionDB::PG_SQL,
            ConnectionDB::PG_SQL_CMS,
            ConnectionDB::PG_SODIUM,
            ConnectionDB::PG_SODIUM_CMS,
        ];
    }

    protected function hideLogStatement()
    {
        $key = SodiumEncryption::keyManager()->getPrefixKeyHex();

        $connections = [ConnectionDB::PG_SODIUM, ConnectionDB::PG_SODIUM_CMS];

        foreach ($connections as $connection) {
            DB::connection($connection)->statement("SET LOCAL log_statement = 'none'");
            DB::connection($connection)->statement("SET LOCAL app.key = '$key'");
            DB::connection($connection)->statement('RESET log_statement');
        }
    }
}
