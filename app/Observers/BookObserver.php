<?php

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    protected function invalidateCache(Book $book): void
    {
        // Invalidate cached ISBN lookup
        Cache::forget("book:isbn:{$book->isbn}");
        // Invalidate category listing caches
        Cache::tags(["category:{$book->category_id}"])->flush();
    }

    public function saved(Book $book): void
    {
        $this->invalidateCache($book);
    }

    public function deleted(Book $book): void
    {
        $this->invalidateCache($book);
    }
}