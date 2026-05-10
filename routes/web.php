<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DataPortabilityController;
use App\Http\Controllers\Admin\AuditLogController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ReviewAnalysisController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Book browsing (public)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Category browsing (public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Email verification routes (from Breeze)
Route::get('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// 2FA Routes
Route::middleware('auth')->group(function () {
    Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('profile.two-factor');
    Route::get('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

Route::middleware('auth')->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
});

// Authenticated routes (require email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/test-export', function () {
    return Excel::download(new \App\Exports\BooksExport, 'books.xlsx');
})->middleware(['auth', 'admin']);

    // Cart routes
    Route::get('/cart', [OrderController::class, 'cart'])->name('orders.cart');
    Route::post('/cart/add/{book}', [OrderController::class, 'addToCart'])->name('orders.cart.add');
    Route::post('/cart/update', [OrderController::class, 'updateCart'])->name('orders.cart.update');
    Route::get('/cart/remove/{book}', [OrderController::class, 'removeFromCart'])->name('orders.cart.remove');
    
    // Checkout
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');

    // Review routes
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// Admin-only routes (require email verification + admin role)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/test-export', function () {
    return Excel::download(new \App\Exports\BooksExport, 'books.xlsx');
})->middleware(['auth', 'admin']);
    
    // Category management
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Book management
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    
    // Admin order management
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.admin');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // Data Portability (Import/Export)
    Route::get('/data-portability', [DataPortabilityController::class, 'index'])->name('data-portability');
    Route::post('/import', [DataPortabilityController::class, 'import'])->name('import');
    Route::get('/export', [DataPortabilityController::class, 'export'])->name('export');
    Route::get('/template', [DataPortabilityController::class, 'template'])->name('template');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{audit}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    Route::post('/books/{book}/analyze-reviews', [ReviewAnalysisController::class, 'analyze'])
        ->name('books.analyze-reviews');
    Route::get('/ai-dashboard', [ReviewAnalysisController::class, 'usageDashboard'])
        ->name('ai-dashboard');
});

require __DIR__.'/auth.php';