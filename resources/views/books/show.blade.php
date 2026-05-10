@extends('layouts.app')

@section('title', $book->title . ' - PageTurner')

@section('content')
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Book Cover -->
            <div class="md:w-1/3 bg-gray-200 p-8 flex items-center justify-center">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" 
                        alt="{{ $book->title }}" 
                        class="max-h-96 object-contain">
                @else
                    <!-- Default placeholder SVG -->
                    <svg class="h-48 w-48 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                @endif
            </div>
            
            <!-- Book Details -->
            <div class="md:w-2/3 p-8">
                <span class="text-brown-600 text-sm font-medium">{{ $book->category->name }}</span>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $book->title }}</h1>
                <p class="text-xl text-gray-600 mt-1">by {{ $book->author }}</p>
                
                <!-- Rating -->
                <div class="flex items-center mt-4">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="h-6 w-6 {{ $i <= round($book->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                    <span class="ml-2 text-gray-600">{{ number_format($book->average_rating, 1) }} ({{ $book->reviews->count() }} reviews)</span>
                </div>
                
                <p class="text-3xl font-bold text-brown-600 mt-4">${{ number_format($book->price, 2) }}</p>
                
                <div class="mt-4">
                    <span class="text-sm {{ $book->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        @if($book->stock_quantity > 0)
                            In Stock ({{ $book->stock_quantity }} available)
                        @else
                            Out of Stock
                        @endif
                    </span>
                </div>
                
                <div class="mt-4">
                    <p class="text-gray-600 text-sm"><strong>ISBN:</strong> {{ $book->isbn }}</p>
                </div>
                
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-800">Description</h3>
                    <p class="text-gray-600 mt-2">{{ $book->description }}</p>
                </div>
                
                <!-- Add to Cart Section -->
                @auth
                    @if($book->stock_quantity > 0)
                        <div class="mt-6 border-t pt-6">
                            <form action="{{ route('orders.cart.add', $book) }}" method="POST" class="flex items-end space-x-4">
                                @csrf
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $book->stock_quantity }}"
                                           class="w-20 border-gray-300 rounded-md shadow-sm focus:ring-brown-500 focus:border-brown-500">
                                </div>
                                <button type="submit" 
                                        class="bg-brown-600 text-white px-6 py-2 rounded-md hover:bg-brown-700 transition flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-6 border-t pt-6">
                            <button disabled class="bg-gray-400 text-white px-6 py-2 rounded-md cursor-not-allowed flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Out of Stock
                            </button>
                        </div>
                    @endif
                @else
                    <div class="mt-6 border-t pt-6">
                        <a href="{{ route('login') }}" class="text-brown-600 hover:text-brown-800 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login to add items to cart
                        </a>
                    </div>
                @endauth
                
                <!-- Admin Actions -->
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="mt-6 flex space-x-4">
                            <a href="{{ route('admin.books.edit', $book) }}" 
                               class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">
                                Edit Book
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this book?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                                    Delete Book
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>
        
        <!-- Review Form (for authenticated users) -->
        @auth
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="font-semibold text-lg mb-4">Write a Review</h3>
                <form action="{{ route('reviews.store', $book) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Rating</label>
                        <select name="rating" class="border-gray-300 rounded-md shadow-sm focus:ring-brown-500 focus:border-brown-500" required>
                            <option value="">Select rating</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Comment</label>
                        <textarea name="comment" rows="4" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-brown-500 focus:border-brown-500"
                                  placeholder="Share your thoughts about this book..."></textarea>
                    </div>
                    
                    <button type="submit" class="bg-brown-600 text-white px-6 py-2 rounded hover:bg-brown-700 transition">
                        Submit Review
                    </button>
                </form>
            </div>
        @else
            <x-alert type="info" class="mb-6">
                <a href="{{ route('login') }}" class="text-brown-600 hover:underline">Login</a> to write a review.
            </x-alert>
        @endauth
        
        <!-- Display Reviews -->
        @forelse($book->reviews as $review)
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold">{{ $review->user->name }}</p>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-500 text-sm">{{ $review->created_at->diffForHumans() }}</span>
                        
                        @auth
                            @if(auth()->id() === $review->user_id || auth()->user()->isAdmin())
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
                
                @isset($review->comment)
                    <p class="text-gray-600 mt-3">{{ $review->comment }}</p>
                @endisset
            </div>
        @empty
            <x-alert type="info">
                No reviews yet. Be the first to review this book!
            </x-alert>
        @endforelse
    </div>
{{-- AI Review Analysis Section --}}
    @if($book->aiAnalysis)
        @php $analysis = $book->aiAnalysis; @endphp
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    AI Review Analysis
                    <span class="text-xs font-normal text-gray-400 ml-1">via {{ ucfirst($analysis->provider_used) }}</span>
                </h3>
                @auth
                    @if(auth()->user()->isAdmin())
                        <form action="{{ route('admin.books.analyze-reviews', $book) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="text-xs bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                                Re-analyze
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            {{-- Sentiment Badge --}}
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($analysis->overall_sentiment === 'positive') bg-green-100 text-green-800
                    @elseif($analysis->overall_sentiment === 'negative') bg-red-100 text-red-800
                    @elseif($analysis->overall_sentiment === 'mixed') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($analysis->overall_sentiment) }} Sentiment
                </span>
                <p class="text-xs text-gray-400">
                    Based on {{ $analysis->reviews_analyzed }} review(s)
                    &bull;
                    Score: {{ number_format($analysis->sentiment_score * 100, 0) }}%
                </p>
            </div>

            {{-- Summary --}}
            <p class="text-gray-700 text-sm leading-relaxed mb-4">{{ $analysis->summary }}</p>

            {{-- Sentiment Breakdown --}}
            @if($analysis->sentiment_breakdown)
                @php $breakdown = $analysis->sentiment_breakdown; @endphp
                <div class="mb-4">
                    <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">Sentiment Breakdown</p>
                    <div class="flex rounded-full overflow-hidden h-3">
                        @if(($breakdown['positive'] ?? 0) > 0)
                            <div class="bg-green-400 h-full" style="width: {{ $breakdown['positive'] }}%"
                                 title="Positive: {{ $breakdown['positive'] }}%"></div>
                        @endif
                        @if(($breakdown['neutral'] ?? 0) > 0)
                            <div class="bg-gray-300 h-full" style="width: {{ $breakdown['neutral'] }}%"
                                 title="Neutral: {{ $breakdown['neutral'] }}%"></div>
                        @endif
                        @if(($breakdown['negative'] ?? 0) > 0)
                            <div class="bg-red-400 h-full" style="width: {{ $breakdown['negative'] }}%"
                                 title="Negative: {{ $breakdown['negative'] }}%"></div>
                        @endif
                    </div>
                    <div class="flex gap-4 mt-1 text-xs text-gray-500">
                        <span>Positive: {{ $breakdown['positive'] ?? 0 }}%</span>
                        <span>Neutral: {{ $breakdown['neutral'] ?? 0 }}%</span>
                        <span>Negative: {{ $breakdown['negative'] ?? 0 }}%</span>
                    </div>
                </div>
            @endif

            {{-- Key Themes --}}
            @if($analysis->key_themes && count($analysis->key_themes) > 0)
                <div>
                    <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">Key Themes</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($analysis->key_themes as $theme)
                            <span class="bg-indigo-50 text-indigo-700 text-xs px-2 py-1 rounded-full">
                                {{ $theme }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <p class="text-xs text-gray-400 mt-4">Last analyzed: {{ $analysis->updated_at->diffForHumans() }}</p>
        </div>

    @elseif(auth()->check() && auth()->user()->isAdmin())
        <div class="bg-white rounded-lg shadow p-6 mt-6 border-2 border-dashed border-gray-200 text-center">
            <p class="text-gray-500 mb-3">No AI analysis available for this book's reviews.</p>
            @if($book->reviews->count() > 0)
                <form action="{{ route('admin.books.analyze-reviews', $book) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
                        Generate AI Analysis
                    </button>
                </form>
            @else
                <p class="text-xs text-gray-400">Add some reviews first to enable analysis.</p>
            @endif
        </div>
    @endif

@endsection