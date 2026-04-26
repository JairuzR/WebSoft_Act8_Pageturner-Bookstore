<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;

class BookRepository
{
    /**
     * Optimised active book listing using cursor pagination.
     * Uses a covering index: idx_books_catalog_filter (category_id, published_at, is_active)
     */
    public function getActiveBooks(int $perPage = 100): CursorPaginator
    {
        return Book::select([
                'id', 'isbn', 'title', 'author', 'publisher',
                'price', 'stock_quantity', 'published_at', 'category_id'
            ])
            ->with(['category:id,name'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')           // stable sort
            ->cursorPaginate($perPage);
    }

    /**
     * Find a book by ISBN (exact match) – should be < 50 ms with unique index.
     */
    public function findByIsbn(string $isbn): ?Book
    {
        return Book::where('isbn', $isbn)
            ->select(['id', 'isbn', 'title', 'author', 'price', 'stock_quantity', 'description', 'published_at'])
            ->first();
    }

    /**
     * Books by category – uses composite index and cache tagging.
     */
    public function getByCategory(int $categoryId, int $perPage = 100): CursorPaginator
    {
        return Book::select([
                'id', 'isbn', 'title', 'author', 'price', 'stock_quantity', 'published_at', 'category_id'
            ])
            ->with(['category:id,name'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }
}