<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();

        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Order status summary
        $orderStatusSummary = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Recent reviews
        $recentReviews = Review::with(['user', 'book'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly sales data (for chart)
        $monthlySales = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('sum(total_amount) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top selling books
        $topBooks = Book::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalBooks',
            'totalCategories',
            'totalOrders',
            'recentOrders',
            'orderStatusSummary',
            'recentReviews',
            'monthlySales',
            'topBooks'
        ));
    }
}