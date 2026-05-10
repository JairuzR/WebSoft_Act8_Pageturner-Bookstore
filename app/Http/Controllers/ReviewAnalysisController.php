<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeBookReviews;
use App\Models\AiReviewAnalysis;
use App\Models\AiUsageLog;
use App\Models\Book;
use Illuminate\Http\Request;

class ReviewAnalysisController extends Controller
{
    // Admin triggers a (re)analysis
    public function analyze(Book $book)
    {
        if ($book->reviews()->count() < 1) {
            return back()->with('error', 'This book has no reviews to analyze.');
        }

        AnalyzeBookReviews::dispatch($book)->onQueue('ai-tasks');

        return back()->with('success', 'AI analysis queued! Refresh in a few seconds.');
    }

    // Admin dashboard: usage stats
    public function usageDashboard()
    {
        $stats = AiUsageLog::selectRaw('
            provider,
            COUNT(*) as total_calls,
            SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed,
            SUM(input_tokens) as total_input_tokens,
            SUM(output_tokens) as total_output_tokens,
            SUM(cost_estimate) as total_cost
        ')
        ->groupBy('provider')
        ->get();

        $recentLogs = AiUsageLog::latest()->take(20)->get();

        $analyses = AiReviewAnalysis::with('book')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.ai-dashboard', compact('stats', 'recentLogs', 'analyses'));
    }
}