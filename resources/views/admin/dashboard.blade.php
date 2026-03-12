@extends('layouts.app')

@section('title', 'Admin Dashboard - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Books Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Books</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalBooks }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Categories</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalCategories }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="py-2 text-sm">#{{ $order->id }}</td>
                            <td class="py-2 text-sm">{{ $order->user->name }}</td>
                            <td class="py-2 text-sm font-medium text-green-600">${{ number_format($order->total_amount, 2) }}</td>
                            <td class="py-2 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ route('admin.orders.admin') }}" class="mt-4 inline-block text-green-600 hover:text-green-800 text-sm">
                View All Orders →
            </a>
        </div>

        <!-- Order Status Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Order Status Summary</h2>
            
            <div class="space-y-3">
                @foreach($orderStatusSummary as $status)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ ucfirst($status->status) }}</span>
                        <span class="font-medium">{{ $status->total }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full 
                            @if($status->status == 'completed') bg-green-600
                            @elseif($status->status == 'pending') bg-yellow-600
                            @elseif($status->status == 'processing') bg-blue-600
                            @elseif($status->status == 'cancelled') bg-red-600
                            @endif"
                            style="width: {{ ($status->total / $totalOrders) * 100 }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Reviews -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Reviews</h2>
            
            @foreach($recentReviews as $review)
            <div class="border-b last:border-0 py-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $review->book->title }}</p>
                        <p class="text-sm text-gray-600">by {{ $review->user->name }}</p>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Top Selling Books -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Top Selling Books</h2>
            
            @foreach($topBooks as $book)
            <div class="flex items-center justify-between border-b last:border-0 py-3">
                <div>
                    <p class="font-medium">{{ $book->title }}</p>
                    <p class="text-sm text-gray-600">by {{ $book->author }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium text-green-600">{{ $book->order_items_count }} sold</p>
                    <p class="text-xs text-gray-500">${{ number_format($book->price, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection