<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Order statistics
        $totalOrders = Order::where('user_id', $user->id)->count();
        $recentOrders = Order::where('user_id', $user->id)
            ->with('orderItems.book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Order status counts
        $pendingOrders = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Recent reviews
        $recentReviews = Review::where('user_id', $user->id)
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Recently purchased books (unique books from recent orders)
        $recentBooks = Order::where('user_id', $user->id)
            ->with('orderItems.book')
            ->where('status', 'completed')
            ->latest()
            ->take(3)
            ->get()
            ->pluck('orderItems')
            ->flatten()
            ->pluck('book')
            ->unique('id')
            ->take(4);
        
        return view('user.dashboard', compact(
            'user',
            'totalOrders',
            'recentOrders',
            'pendingOrders',
            'completedOrders',
            'recentReviews',
            'recentBooks'
        ));
    }
}