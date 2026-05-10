<nav class="bg-brown-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-xl font-bold">PageTurner</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex ml-10 space-x-4">
                    <a href="{{ route('home') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                        Home
                    </a>
                    <a href="{{ route('books.index') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                        Books
                    </a>
                    <a href="{{ route('categories.index') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                        Categories
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.books.create') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                                Add Book
                            </a>
                            <a href="{{ route('admin.data-portability') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                                Data Portability
                            </a>
                            <a href="{{ route('admin.audit-logs.index') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                                Audit Logs
                            </a>
                            <a href="{{ route('admin.categories.create') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                                Add Category
                            </a>
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.ai-dashboard') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                                        AI Dashboard
                                    </a>
                                @endif
                            @endauth
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-white text-brown-600 px-4 py-2 rounded-md font-medium">
                        Register
                    </a>
                @endguest

                @auth
                    <!-- Dashboard Link -->
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                            Dashboard
                        </a>
                    @endif

                    <!-- Cart Icon with Counter -->
                    <a href="{{ route('orders.cart') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md flex items-center relative">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="ml-1">Cart</span>
                        @php
                            $cartService = app(\App\Services\CartService::class);
                            $cartCount = $cartService->getCount();
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full px-1.5 py-0.5 text-xs font-bold min-w-[20px] text-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    
                    <a href="{{ route('orders.index') }}" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                        My Orders
                    </a>
                    
                    <!-- Security Indicator -->
                    @if(auth()->user()->hasTwoFactorEnabled())
                        <span class="text-green-300" title="2FA Enabled">
                            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                    @endif
                    
                    <span class="text-brown-200">{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:bg-brown-700 px-3 py-2 rounded-md">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>