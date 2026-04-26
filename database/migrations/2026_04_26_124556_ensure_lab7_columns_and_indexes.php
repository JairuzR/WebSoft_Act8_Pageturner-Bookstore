<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('books', 'publisher')) {
                $table->string('publisher')->nullable()->after('isbn');
            }
            if (!Schema::hasColumn('books', 'format')) {
                $table->string('format')->default('paperback')->after('publisher');
            }
            if (!Schema::hasColumn('books', 'published_at')) {
                $table->dateTime('published_at')->nullable()->after('format');
            }
            if (!Schema::hasColumn('books', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('published_at');
            }
        });

        // Add indexes in a separate Schema::table call to avoid conflicts
        Schema::table('books', function (Blueprint $table) {
            // Only create indexes if they do not already exist
            $indexes = DB::select('SHOW INDEXES FROM books');
            $existingIndexNames = array_map(fn($i) => $i->Key_name, $indexes);

            if (!in_array('idx_books_catalog_filter', $existingIndexNames)) {
                $table->index(['category_id', 'published_at', 'is_active'], 'idx_books_catalog_filter');
            }
            if (!in_array('idx_books_price_stock', $existingIndexNames)) {
                $table->index(['price', 'stock_quantity', 'id'], 'idx_books_price_stock');
            }
            if (!in_array('idx_books_active', $existingIndexNames)) {
                $table->index('is_active', 'idx_books_active');
            }
            if (!in_array('idx_books_isbn_lookup', $existingIndexNames)) {
                $table->index('isbn', 'idx_books_isbn_lookup');
            }
            // Full‑text index requires a separate check
            $fullTextExists = DB::select("SHOW INDEX FROM books WHERE KEY_NAME = 'idx_books_fulltext'");
            if (empty($fullTextExists)) {
                $table->fullText(['title', 'description'], 'idx_books_fulltext');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('idx_books_catalog_filter');
            $table->dropIndex('idx_books_price_stock');
            $table->dropIndex('idx_books_fulltext');
            $table->dropIndex('idx_books_active');
            $table->dropIndex('idx_books_isbn_lookup');
            $table->dropColumn(['publisher', 'format', 'published_at', 'is_active']);
        });
    }
};