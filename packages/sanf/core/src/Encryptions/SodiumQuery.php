<?php

namespace Sanf\Core\Encryptions;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Constants\ConnectionDB;

class SodiumQuery
{
    private $keyHex;

    public function __construct()
    {
        $this->keyHex = SodiumKeyManager::make()->getPrefixKeyHex();
    }

    public static function make(): self
    {
        return new self();
    }

    private function db()
    {
        return DB::connection(ConnectionDB::PG_SODIUM);
    }

    public function transaction($closure)
    {
        return $this->db()
            ->transaction(function () use ($closure) {
                $this->hideLogStatement();

                return $closure($this);
            });
    }

    public function beginTransaction(): void
    {
        $this->db()->beginTransaction();
    }

    public function multipleBeginTransaction()
    {
        $this->beginTransaction();
        DB::connection(ConnectionDB::PG_SQL)->beginTransaction();
    }

    public function commit(): void
    {
        $this->db()->commit();
    }

    public function multipleCommit()
    {
        $this->commit();
        DB::connection(ConnectionDB::PG_SQL)->commit();
    }

    public function rollBack(): void
    {
        $this->db()->rollBack();
    }

    public function multipleRollBack()
    {
        $this->rollBack();
        DB::connection(ConnectionDB::PG_SQL)->rollBack();
    }

    public function hideLogStatement(): void
    {
        $this->db()->statement("SET LOCAL log_statement = 'none'");
        $this->db()->statement("SET LOCAL app.key = '$this->keyHex'");
        $this->db()->statement('RESET log_statement');
    }

    public function selectRaw($field, $nonce = 'nonce'): Expression
    {
        return $this->db()->raw("convert_from(pgsodium.crypto_secretbox_open($field, $nonce, current_setting('app.key')::bytea), 'utf8')");
    }

    public function selectRawNullable($field, $nonce = 'nonce'): Expression
    {
        return $this->db()
            ->raw("CASE
                WHEN $field IS NOT NULL
                THEN convert_from(pgsodium.crypto_secretbox_open($field, $nonce, current_setting('app.key')::bytea), 'utf8')
                ELSE NULL
            END");
    }
}
