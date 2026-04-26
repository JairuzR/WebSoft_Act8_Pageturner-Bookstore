<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Drop the unique isbn index (can be dropped anytime)
        $hasUniqueIsbn = DB::selectOne("SELECT COUNT(*) as cnt FROM information_schema.STATISTICS WHERE TABLE_NAME = 'books' AND INDEX_NAME = 'books_isbn_unique'");
        if ($hasUniqueIsbn->cnt > 0) {
            DB::statement('ALTER TABLE books DROP INDEX books_isbn_unique');
        }

        // 2. Drop the primary key FIRST (before touching AUTO_INCREMENT)
        $hasPrimaryKey = DB::selectOne("SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'books' AND CONSTRAINT_TYPE = 'PRIMARY KEY'");
        if ($hasPrimaryKey->cnt > 0) {
            DB::statement('ALTER TABLE books DROP PRIMARY KEY');
        }

        // 3. Now safe to remove AUTO_INCREMENT
        DB::statement('ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL');

        // 4. Add composite primary key
        DB::statement('ALTER TABLE books ADD PRIMARY KEY books_id_published_at_primary (id, published_at)');

        // 5. Add new unique index
        DB::statement('ALTER TABLE books ADD UNIQUE INDEX books_isbn_published_at_unique (isbn, published_at)');

        // 6. Restore AUTO_INCREMENT
        DB::statement('ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::statement('ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE books DROP PRIMARY KEY');
        DB::statement('ALTER TABLE books DROP INDEX books_isbn_published_at_unique');

        DB::statement('ALTER TABLE books ADD PRIMARY KEY (id)');
        DB::statement('ALTER TABLE books ADD UNIQUE INDEX books_isbn_unique (isbn)');
        DB::statement('ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

        Schema::enableForeignKeyConstraints();
    }
};