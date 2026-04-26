<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mv_bestseller_stats', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->primary();
            $table->integer('total_books');
            $table->decimal('avg_price', 10, 2);
            $table->integer('total_inventory');
            $table->integer('bestseller_count');
            $table->dateTime('latest_publication')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mv_bestseller_stats');
    }
};