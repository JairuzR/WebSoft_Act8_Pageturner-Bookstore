<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0');

        // MySQL does not support foreign keys on partitioned tables at all.
        // We permanently drop them before partitioning.
        DB::unprepared('ALTER TABLE order_items DROP FOREIGN KEY order_items_book_id_foreign');
        DB::unprepared('ALTER TABLE reviews DROP FOREIGN KEY reviews_book_id_foreign');
        DB::unprepared('ALTER TABLE books DROP FOREIGN KEY books_category_id_foreign');

        // Apply partitioning
        DB::unprepared("ALTER TABLE books
            PARTITION BY RANGE (YEAR(published_at)) (
                PARTITION p_old VALUES LESS THAN (2000),
                PARTITION p_2000 VALUES LESS THAN (2005),
                PARTITION p_2005 VALUES LESS THAN (2010),
                PARTITION p_2010 VALUES LESS THAN (2015),
                PARTITION p_2015 VALUES LESS THAN (2020),
                PARTITION p_2020 VALUES LESS THAN (2025),
                PARTITION p_future VALUES LESS THAN MAXVALUE
            )");

        DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared('ALTER TABLE books REMOVE PARTITIONING');
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
    }
};