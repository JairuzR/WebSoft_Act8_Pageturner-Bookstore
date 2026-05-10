<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\ReviewAnalysisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeBookReviews implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public Book $book) {}

    public function handle(ReviewAnalysisService $service): void
    {
        Log::info("Starting AI review analysis for book ID: {$this->book->id}");

        $analysis = $service->analyzeBookReviews($this->book);

        Log::info("AI review analysis complete for book ID: {$this->book->id}", [
            'sentiment' => $analysis->overall_sentiment,
            'provider'  => $analysis->provider_used,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("AnalyzeBookReviews job failed for book ID: {$this->book->id}", [
            'error' => $exception->getMessage(),
        ]);
    }
}