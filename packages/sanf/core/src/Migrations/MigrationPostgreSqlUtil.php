<?php

namespace Sanf\Core\Migrations;

use Illuminate\Support\Facades\DB;

class MigrationPostgreSqlUtil
{
    protected $dbConnection;

    public function __construct($connection = null)
    {
        $connection = $connection ?: config('database.default');

        $this->dbConnection = DB::connection($connection);
    }

    public static function make($connection = null) {
        return new self($connection);
    }

    public function changeTypeToBytea($table, $column)
    {
        $this->dbConnection->statement("ALTER TABLE {$table} ALTER {$column} TYPE BYTEA USING {$column}::bytea");
    }

    public function changeTypeToText($table, $column)
    {
        $this->dbConnection->statement("ALTER TABLE {$table} ALTER {$column} TYPE TEXT USING {$column}::text");
    }

    public function changeTypeToVarchar($table, $column, $maxLength = 255)
    {
        $this->dbConnection->statement("ALTER TABLE {$table} ALTER {$column} TYPE VARCHAR({$maxLength}) USING {$column}::varchar");
    }

    public function changeTypeToJson($table, $column)
    {
        $this->dbConnection->statement("ALTER TABLE {$table} ALTER {$column} TYPE JSON USING {$column}::json");
    }

    public function changeTypeToJsonb($table, $column)
    {
        $this->dbConnection->statement("ALTER TABLE {$table} ALTER {$column} TYPE JSONB USING {$column}::jsonb");
    }

    public function changeTypeToInet($table, $column)
    {
        $this->dbConnection->statement("ALTER TABLE {$table} ALTER {$column} TYPE INET USING {$column}::inet");
    }

    public function changeComment($table, $column, $comment)
    {
        $this->dbConnection->statement("COMMENT ON COLUMN {$table}.{$column} IS '{$comment}'");
    }

    public function removeComment($table, $column)
    {
        $this->dbConnection->statement("COMMENT ON COLUMN {$table}.{$column} IS NULL");
    }

    public function rearrangeSequence($table, $column = 'id', $sequenceName = null)
    {
        if (is_null($sequenceName)) {
            $sequenceName = "{$table}_{$column}_seq";
        }

        $this->dbConnection
            ->statement("SELECT setval('{$sequenceName}', (SELECT MAX($column) FROM $table) + 1)");
    }
}
