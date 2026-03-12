@extends('layouts.app')

@section('title', 'My Dashboard - PageTurner')

@section('header')
    <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h1>
@endsection

@section('content')
    <!-- Account Status Alerts -->
    <div class="mb-6 space-y-3">
        @if(!$user->hasVerifiedEmail())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>
                        <strong>Email not verified!</strong> 
                        Please verify your email address to access all features.
                        <a href="{{ route('verification.notice') }}" class="underline ml-2">Verify now</a>
                    </span>
                </div>
            </div>
        @endif

        @if(!$user->hasTwoFactorEnabled())
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>
                        <strong>Enhance your security!</strong> 
                        Enable two-factor authentication to protect your account.
                        <a href="{{ route('profile.two-factor') }}" class="underline ml-2">Enable 2FA</a>
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Orders Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Orders</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingOrders }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Orders Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Completed Orders</p>
                    <p class="text-3xl font-bold text-green-600">{{ $completedOrders }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Reviews Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">My Reviews</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $recentReviews->count() }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
            
            @if($recentOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                            <tr>
                                <td class="py-3 text-sm">#{{ $order->id }}</td>
                                <td class="py-3 text-sm">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="py-3 text-sm font-medium text-green-600">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($order->status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="text-green-600 hover:text-green-800">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('orders.index') }}" class="mt-4 inline-block text-green-600 hover:text-green-800 text-sm">
                    View All Orders →
                </a>
            @else
                <p class="text-gray-500 text-center py-4">No orders yet.</p>
                <a href="{{ route('books.index') }}" class="mt-2 inline-block bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Start Shopping
                </a>
            @endif
        </div>

        <!-- Recently Purchased Books -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Purchases</h2>
            
            @if($recentBooks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentBooks as $book)
                        <div class="flex items-center space-x-3 border-b last:border-0 pb-3">
                            <div class="flex-shrink-0 h-12 w-12 bg-gray-200 rounded">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                         alt="{{ $book->title }}"
                                         class="h-12 w-12 object-cover rounded">
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800">{{ Str::limit($book->title, 25) }}</h3>
                                <p class="text-sm text-gray-600">by {{ $book->author }}</p>
                            </div>
                            <a href="{{ route('books.show', $book) }}" class="text-green-600 hover:text-green-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No purchases yet.</p>
            @endif
        </div>

        <!-- Recent Reviews -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">My Recent Reviews</h2>
            
            @if($recentReviews->count() > 0)
                @foreach($recentReviews as $review)
                    <div class="border-b last:border-0 py-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">{{ $review->book->title }}</p>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-gray-600 mt-1">"{{ Str::limit($review->comment, 50) }}"</p>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 text-center py-4">No reviews yet.</p>
            @endif
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            
            <div class="space-y-3">
                <a href="{{ route('books.index') }}" 
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-gray-700">Browse Books</span>
                </a>

                <a href="{{ route('orders.index') }}" 
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="text-gray-700">Order History</span>
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-gray-700">Profile Settings</span>
                </a>

                <a href="{{ route('profile.two-factor') }}" 
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-gray-700">Security Settings</span>
                </a>
            </div>
        </div>
    </div>
@endsection