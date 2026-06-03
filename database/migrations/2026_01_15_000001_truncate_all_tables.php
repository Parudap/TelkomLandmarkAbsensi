<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TruncateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Get all table names
        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');

        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_$dbName"};
            DB::table($tableName)->truncate();
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No rollback for truncate
    }
}