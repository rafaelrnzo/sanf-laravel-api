<?php

namespace Sanf\Core\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationForeignKeyUtil
{
    protected $connection;

    public function __construct($connection = null)
    {
        $connection = $connection ?: config('database.default');

        $this->connection = $connection;
    }

    public static function make($connection = null) {
        return new self($connection);
    }

    public function foreignKeyName($table, $referenceColumn): string
    {
        $table = strtolower($table);
        $referenceColumn = strtolower($referenceColumn);

        return substr("{$table}_{$referenceColumn}_foreign", 0, 63);
    }

    public function listTableForeignKeys($table): array
    {
        $conn = Schema::connection($this->connection)
            ->getConnection()
            ->getDoctrineSchemaManager();

        return array_map(function ($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }

    public function removeForeignKey($table, $referenceColumn): void
    {
        $foreignKeyName = $this->foreignKeyName($table, $referenceColumn);

        Schema::connection($this->connection)->table(
            $table,
            function (Blueprint $queryTable) use ($foreignKeyName) {
                $queryTable->dropForeign($foreignKeyName);
            }
        );
    }

    public function removeForeignKeyIfExists($table, $referenceColumn): void
    {
        $foreignKeyName = $this->foreignKeyName($table, $referenceColumn);

        if (!$this->foreignKeyExists($table, $foreignKeyName)) {
            return;
        }

        Schema::connection($this->connection)->table(
            $table,
            function (Blueprint $queryTable) use ($foreignKeyName) {
                $queryTable->dropForeign($foreignKeyName);
            }
        );
    }

    public function foreignKeyExists($table, $foreignKeyName): bool
    {
        return in_array($foreignKeyName, $this->listTableForeignKeys($table));
    }
}
