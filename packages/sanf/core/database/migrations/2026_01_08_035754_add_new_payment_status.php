<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNewPaymentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->updatePostgresEnumConstraint('payment', 'status', ['PENDING', 'SUCCESS', 'FAILED', 'EXPIRED', 'CANCELLED', 'EXPIRE_IN_PROGRESS', 'PAID_LATE']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->updatePostgresEnumConstraint('payment', 'status', ['PENDING', 'SUCCESS', 'FAILED', 'EXPIRED', 'CANCELLED']);
    }

    protected function updatePostgresEnumConstraint(string $table, string $column, array $allowed): void
    {
        $constraintName = sprintf('%s_%s_check', $table, $column);
        $quotedAllowed = implode(',', array_map(function ($value) {
            return "'" . str_replace("'", "''", $value) . "'";
        }, $allowed));

        DB::statement(sprintf('ALTER TABLE "%s" DROP CONSTRAINT IF EXISTS "%s"', $table, $constraintName));
        DB::statement(sprintf('ALTER TABLE "%s" ADD CONSTRAINT "%s" CHECK ("%s" IN (%s))', $table, $constraintName, $column, $quotedAllowed));
    }
}
