<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class BenchmarkBookQueries extends Command
{
    protected $signature = 'benchmark:books {--iterations=100}';
    protected $description = 'Benchmark critical book queries';

    public function handle()
    {
        $iterations = (int) $this->option('iterations');
        $this->info("Running benchmarks ({$iterations} iterations)...\n");

        // 1. ISBN lookup
        $randomIsbn = Book::inRandomOrder()->value('isbn');
        $time = $this->benchmark(function () use ($randomIsbn) {
            Book::where('isbn', $randomIsbn)->first();
        }, $iterations);
        $this->info("ISBN lookup: {$time} ms");

        // 2. Cursor pagination
        $time = $this->benchmark(function () {
            Book::select(['id', 'title', 'author', 'price', 'stock_quantity', 'published_at'])
                ->where('is_active', true)
                ->orderBy('published_at', 'desc')
                ->cursorPaginate(100);
        }, $iterations);
        $this->info("Catalog listing (100 per page): {$time} ms");

        // 3. Category filter
        $categoryId = Book::inRandomOrder()->value('category_id');
        $time = $this->benchmark(function () use ($categoryId) {
            Book::where('category_id', $categoryId)
                ->where('is_active', true)
                ->orderBy('published_at', 'desc')
                ->cursorPaginate(100);
        }, $iterations);
        $this->info("Category filter: {$time} ms");

        // 4. Full‑text search (fewer iterations)
        $time = $this->benchmark(function () {
            Book::search('Penguin')->take(100)->get();
        }, min(10, $iterations));
        $this->info("Full‑text search (100 results): {$time} ms");

        $this->info("\nBenchmark complete.");
        return Command::SUCCESS;
    }

    private function benchmark(callable $callback, int $iterations): float
    {
        // Warmup (5 runs)
        for ($i = 0; $i < 5; $i++) {
            $callback();
        }

        $times = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $callback();
            $times[] = (microtime(true) - $start) * 1000; // milliseconds
        }

        return round(array_sum($times) / count($times), 2);
    }
}