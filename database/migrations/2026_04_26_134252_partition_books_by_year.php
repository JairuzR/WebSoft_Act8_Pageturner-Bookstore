<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement("ALTER TABLE books
            PARTITION BY RANGE (YEAR(published_at)) (
                PARTITION p_old VALUES LESS THAN (2000),
                PARTITION p_2000 VALUES LESS THAN (2005),
                PARTITION p_2005 VALUES LESS THAN (2010),
                PARTITION p_2010 VALUES LESS THAN (2015),
                PARTITION p_2015 VALUES LESS THAN (2020),
                PARTITION p_2020 VALUES LESS THAN (2025),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            )");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE books REMOVE PARTITIONING');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};