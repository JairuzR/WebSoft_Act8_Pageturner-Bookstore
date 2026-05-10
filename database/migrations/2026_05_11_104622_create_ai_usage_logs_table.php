<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // gemini, ollama
            $table->string('feature'); // review_analysis
            $table->string('model_used');
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->decimal('cost_estimate', 10, 6)->default(0); // USD
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->nullableMorphs('loggable');
            $table->timestamps();

            $table->index('provider');
            $table->index('feature');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};