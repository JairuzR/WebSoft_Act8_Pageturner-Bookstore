<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_review_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id'); // no foreignId constraint due to partitioned books table
            $table->text('summary');
            $table->string('overall_sentiment');
            $table->decimal('sentiment_score', 4, 2);
            $table->json('sentiment_breakdown');
            $table->json('key_themes')->nullable();
            $table->integer('reviews_analyzed');
            $table->string('provider_used');
            $table->softDeletes();
            $table->timestamps();

            $table->index('book_id');
            $table->index('overall_sentiment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_review_analyses');
    }
};