<?php

namespace App\Providers;

use App\Models\Book;
use App\Observers\BookObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\AIServiceManager::class);
        $this->app->singleton(\App\Services\ReviewAnalysisService::class);
    }

    public function boot(): void
    {
        Book::observe(BookObserver::class);
        
        // Per-second API rate limiting with user tiers
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            
            // Define limits based on user role/tier
            if ($user) {
                if ($user->isAdmin()) {
                    // Admin: 100 requests per minute
                    return Limit::perMinute(100)->by($user->id);
                } elseif ($user->hasVerifiedEmail()) {
                    // Verified customers: 60 requests per minute
                    return Limit::perMinute(60)->by($user->id);
                } else {
                    // Unverified users: 30 requests per minute
                    return Limit::perMinute(30)->by($user->id);
                }
            }
            
            // Guest users: 10 requests per minute
            return Limit::perMinute(10)->by($request->ip());
        });

        // More granular per-second limiter for sensitive endpoints
        RateLimiter::for('sensitive', function (Request $request) {
            $user = $request->user();
            if ($user && $user->isAdmin()) {
                return Limit::perSecond(5)->by($user->id);
            }
            return Limit::perSecond(1)->by($request->ip());
        });

        // Login rate limiter (already exists, enhance for per-second)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response('Too many login attempts.', 429, $headers);
                });
        });
    }
}