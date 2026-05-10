<?php

namespace App\Services;

use App\Models\AiReviewAnalysis;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReviewAnalysisService
{
    public function __construct(private AIServiceManager $ai) {}

    public function analyzeBookReviews(Book $book): AiReviewAnalysis
    {
        $reviews = $book->reviews()->with('user')->latest()->take(
            config('ai.features.review_analysis.max_reviews_in_prompt', 30)
        )->get();

        $minReviews = config('ai.features.review_analysis.min_reviews', 1);

        if ($reviews->count() < $minReviews) {
            throw new \RuntimeException("Not enough reviews to analyze (minimum: {$minReviews}).");
        }

        $prompt = $this->buildPrompt($book, $reviews);

        $result = $this->ai->generateWithFallback($prompt, 'review_analysis');

        $parsed = $this->parseResponse($result['text']);

        // Delete old analysis for this book before creating new one
        AiReviewAnalysis::where('book_id', $book->id)->delete();

        $analysis = AiReviewAnalysis::create([
            'book_id'             => $book->id,
            'summary'             => $parsed['summary'],
            'overall_sentiment'   => $parsed['overall_sentiment'],
            'sentiment_score'     => $parsed['sentiment_score'],
            'sentiment_breakdown' => $parsed['sentiment_breakdown'],
            'key_themes'          => $parsed['key_themes'],
            'reviews_analyzed'    => $reviews->count(),
            'provider_used'       => $result['provider'],
        ]);

        // Bust the cache
        Cache::forget("book_analysis_{$book->id}");

        Log::channel('ai_audit')->info('AI Review Analysis Completed', [
            'feature'       => 'review_analysis',
            'book_id'       => $book->id,
            'book_title'    => $book->title,
            'reviews_count' => $reviews->count(),
            'provider_used' => $result['provider'],
            'sentiment'     => $parsed['overall_sentiment'],
            'timestamp'     => now()->toIso8601String(),
        ]);

        return $analysis;
    }

    private function buildPrompt(Book $book, $reviews): string
    {
        $reviewLines = $reviews->map(function ($review, $index) {
            $rating  = $review->rating;
            $comment = $review->comment ? strip_tags($review->comment) : '(no comment)';
            return "Review " . ($index + 1) . " [Rating: {$rating}/5]: {$comment}";
        })->implode("\n");

        return <<<PROMPT
You are an expert book review analyst. Analyze the following customer reviews for the book "{$book->title}" by {$book->author}.

REVIEWS:
{$reviewLines}

Provide your analysis in EXACTLY this JSON format (no markdown, no code blocks, raw JSON only):
{
  "summary": "A 2-3 sentence summary of the overall reader experience and what customers say about this book.",
  "overall_sentiment": "positive|negative|neutral|mixed",
  "sentiment_score": 0.85,
  "sentiment_breakdown": {
    "positive": 70,
    "neutral": 20,
    "negative": 10
  },
  "key_themes": ["theme one", "theme two", "theme three"]
}

Rules:
- overall_sentiment must be exactly one of: positive, negative, neutral, mixed
- sentiment_score must be a decimal between 0.00 and 1.00 (1.00 = most positive)
- sentiment_breakdown values must be integers that add up to 100
- key_themes must be 2 to 5 short phrases (max 4 words each)
- Return ONLY the JSON object, nothing else
PROMPT;
    }

    private function parseResponse(string $rawText): array
    {
        // Strip any accidental markdown code fences
        $clean = preg_replace('/```(?:json)?\s*|\s*```/', '', $rawText);
        $clean = trim($clean);

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('AI review analysis JSON parse failed', [
                'raw'   => $rawText,
                'error' => json_last_error_msg(),
            ]);
            // Return a safe fallback structure so the job doesn't fail hard
            return [
                'summary'             => 'Analysis could not be parsed. Please try again.',
                'overall_sentiment'   => 'neutral',
                'sentiment_score'     => 0.50,
                'sentiment_breakdown' => ['positive' => 33, 'neutral' => 34, 'negative' => 33],
                'key_themes'          => [],
            ];
        }

        // Sanitize and validate
        $validSentiments = ['positive', 'negative', 'neutral', 'mixed'];
        $sentiment       = in_array($data['overall_sentiment'] ?? '', $validSentiments)
            ? $data['overall_sentiment']
            : 'neutral';

        return [
            'summary'             => substr($data['summary'] ?? 'No summary available.', 0, 1000),
            'overall_sentiment'   => $sentiment,
            'sentiment_score'     => min(1.00, max(0.00, (float) ($data['sentiment_score'] ?? 0.50))),
            'sentiment_breakdown' => $data['sentiment_breakdown'] ?? ['positive' => 33, 'neutral' => 34, 'negative' => 33],
            'key_themes'          => array_slice($data['key_themes'] ?? [], 0, 5),
        ];
    }
}