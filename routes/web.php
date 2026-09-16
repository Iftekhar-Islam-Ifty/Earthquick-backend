<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/* =========================================================================
 * WEB ROUTE ARCHITECTURE - EARTHQUICK (NOUS TELOS)
 * Defines public catalog browsing, customer checkout flows, authenticated
 * account management, and administrative control panel endpoints.
 * ========================================================================= */

/* =========================================================================
 * 1. HOMEPAGE & BRAND SHOWCASE
 * Curated editorial showcase, flagship handloom atelier, and hero banners.
 * ========================================================================= */
Route::get('/', [HomeController::class, 'index'])->name('home');

/* =========================================================================
 * 2. CATEGORY & SUBCATEGORY CATALOGS
 * Dynamic product catalogs with price, fabric, and inventory filters.
 * ========================================================================= */
Route::get('/shop/{slug}', [CategoryController::class, 'showCategory'])->name('category.show');
Route::get('/shop/{categorySlug}/{subcategorySlug}', [CategoryController::class, 'showSubcategory'])->name('subcategory.show');

/* =========================================================================
 * 3. PRODUCT DETAIL & ATELIER SHOWCASE
 * Detailed individual SKU showcase, image galleries, and recommendations.
 * ========================================================================= */
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

/* =========================================================================
 * 4. INSTITUTIONAL & STATIC BRAND PAGES
 * Brand philosophy, artisan collective story, and customer care info.
 * ========================================================================= */
Route::get('/about', [PageController::class, 'about'])->name('about');

/* =========================================================================
 * 5. SHOPPING CART & DRAWER SYNCHRONIZATION
 * Session-persisted cart endpoints supporting AJAX drawer updates.
 * ========================================================================= */
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

/* =========================================================================
 * 6. CHECKOUT & ATOMIC ORDER SUBMISSION
 * Checkout formulation, delivery zone calculation, and order placement.
 * ========================================================================= */
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/order', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

/* =========================================================================
 * 7. CUSTOMER AUTHENTICATION
 * Secure customer login, self-registration, and session termination.
 * ========================================================================= */
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* =========================================================================
 * 8. CUSTOMER DASHBOARD & ORDER HISTORY
 * Authenticated customer account hub, address management, and order tracking.
 * ========================================================================= */
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.dashboard');
    Route::get('/account/order/{order_number}', [AccountController::class, 'showOrder'])->name('account.order');
    Route::post('/account/profile/update', [AccountController::class, 'updateProfile'])->name('account.profile.update');
});

/* =========================================================================
 * 9. LIVE INSTANT SEARCH & RESULTS ENGINE
 * Real-time typeahead suggestions API and paginated full-text search.
 * ========================================================================= */
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

/* =========================================================================
 * 10. ADMINISTRATIVE CONTROL PANEL
 * Protected management suite for orders, inventory statuses, and metrics.
 * ========================================================================= */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/export', [AdminController::class, 'exportOrders'])->name('orders.export');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/orders/{id}/invoice', [AdminController::class, 'orderInvoice'])->name('orders.invoice');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    Route::post('/products/{id}/toggle-stock', [AdminController::class, 'toggleStock'])->name('products.toggle-stock');

    // Coupon & Promotional Campaign Management
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
    Route::get('/coupons/create', [AdminController::class, 'couponCreate'])->name('coupons.create');
    Route::post('/coupons', [AdminController::class, 'couponStore'])->name('coupons.store');
    Route::post('/coupons/{id}/toggle', [AdminController::class, 'couponToggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [AdminController::class, 'couponDestroy'])->name('coupons.destroy');
});