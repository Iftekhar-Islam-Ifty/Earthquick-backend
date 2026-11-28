<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEmailDeliveryController;
use App\Http\Controllers\AdminHomepageImageController;
use App\Http\Controllers\AdminVendorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminSecurityController;
use App\Http\Controllers\AdminPasswordConfirmationController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordRecoveryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderCancellationController;
use App\Http\Controllers\OrderReturnController;
use App\Http\Controllers\OrderRefundController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SupportInquiryController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\AdminNewsletterSubscriptionController;
use App\Http\Controllers\AdminNewsletterCampaignController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

/* =========================================================================
 * WEB ROUTE ARCHITECTURE - RTHQUICK MARKETPLACE
 * Defines public catalog browsing, customer checkout flows, authenticated
 * account management, and administrative control panel endpoints.
 * ========================================================================= */

/* =========================================================================
 * 1. HOMEPAGE & STORE SHOWCASE
 * Curated editorial showcase, flagship handloom atelier, and hero banners.
 * ========================================================================= */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

/* =========================================================================
 * 2. CATEGORY & SUBCATEGORY CATALOGS
 * Dynamic product catalogs with price, fabric, and inventory filters.
 * ========================================================================= */
Route::get('/shop', function () {
    return redirect()->route('stores.index');
})->name('shop.index');
Route::get('/shop/collections/{group}', [CategoryController::class, 'showGroup'])->name('category.group');
Route::get('/shop/{slug}', [CategoryController::class, 'showCategory'])->name('category.show');
Route::get('/shop/{categorySlug}/{subcategorySlug}', [CategoryController::class, 'showSubcategory'])->name('subcategory.show');

/* =========================================================================
 * 2b. STORES & MULTI-VENDOR SHOWCASE
 * Public directory of stores and dedicated storefront pages.
 * ========================================================================= */
Route::get('/stores', [VendorController::class, 'index'])->name('stores.index');
Route::get('/stores/{slug}/catalog', [VendorController::class, 'catalog'])->name('stores.catalog');
Route::get('/stores/bright/about', [PageController::class, 'brightInformation'])->name('stores.bright-information');
Route::get('/stores/{slug}', [VendorController::class, 'show'])->name('stores.show');

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
Route::get('/delivery-returns', [PageController::class, 'deliveryReturns'])->name('policies.delivery-returns');
Route::post('/contact', [SupportInquiryController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/newsletter/nous-telos', [NewsletterSubscriptionController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');
Route::get('/newsletter/confirm/{subscription}/{token}', [NewsletterSubscriptionController::class, 'confirmForm'])->name('newsletter.confirm');
Route::post('/newsletter/confirm/{subscription}/{token}', [NewsletterSubscriptionController::class, 'confirm'])->middleware('throttle:5,1')->name('newsletter.confirm.submit');
Route::get('/newsletter/unsubscribe/{subscription}', [NewsletterSubscriptionController::class, 'unsubscribeForm'])->middleware('signed')->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{subscription}', [NewsletterSubscriptionController::class, 'unsubscribe'])->middleware(['signed', 'throttle:5,1'])->name('newsletter.unsubscribe.submit');

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
Route::post('/checkout/order', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/orders/{order_number}/cancellation-request', [OrderCancellationController::class, 'store'])->name('orders.cancellation-request');
Route::post('/orders/{order_number}/items/{itemId}/return-request', [OrderReturnController::class, 'store'])->name('orders.return-request');

/* =========================================================================
 * 7. CUSTOMER AUTHENTICATION
 * Secure customer login, self-registration, and session termination.
 * ========================================================================= */
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [PasswordRecoveryController::class, 'requestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordRecoveryController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [PasswordRecoveryController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordRecoveryController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

/* =========================================================================
 * 8. CUSTOMER DASHBOARD & ORDER HISTORY
 * Authenticated customer account hub, address management, and order tracking.
 * ========================================================================= */
Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/account', [AccountController::class, 'index'])->name('account.dashboard');
    Route::get('/account/order/{order_number}', [AccountController::class, 'showOrder'])->name('account.order');
    Route::post('/account/profile/update', [AccountController::class, 'updateProfile'])->name('account.profile.update');
});

/* =========================================================================
 * 9. LIVE INSTANT SEARCH & RESULTS ENGINE
 * Real-time typeahead suggestions API and paginated full-text search.
 * ========================================================================= */
Route::get('/search', [SearchController::class, 'index'])->middleware('throttle:60,1')->name('search');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->middleware('throttle:60,1')->name('search.suggestions');

/* =========================================================================
 * 10. ADMINISTRATIVE CONTROL PANEL
 * Protected management suite for orders, inventory statuses, and metrics.
 * ========================================================================= */
Route::middleware(['auth', 'auth.session'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/security/setup', [AdminSecurityController::class, 'setup'])->name('security.setup');
    Route::post('/security/setup', [AdminSecurityController::class, 'confirmSetup'])->middleware('throttle:5,1')->name('security.setup.confirm');
    Route::get('/security/recovery-codes', [AdminSecurityController::class, 'recoveryCodes'])->name('security.recovery-codes');
    Route::get('/security/challenge', [AdminSecurityController::class, 'challenge'])->name('security.challenge');
    Route::post('/security/challenge', [AdminSecurityController::class, 'verifyChallenge'])->middleware('throttle:10,5')->name('security.challenge.verify');
    Route::get('/security/confirm-password', [AdminPasswordConfirmationController::class, 'show'])->name('security.confirm-password');
    Route::post('/security/confirm-password', [AdminPasswordConfirmationController::class, 'confirm'])->middleware('throttle:10,5')->name('security.confirm-password.submit');
});

Route::middleware(['auth', 'admin', 'auth.session'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/homepage-images', [AdminHomepageImageController::class, 'index'])->name('homepage-images.index');
    Route::post('/homepage-images/{slot}', [AdminHomepageImageController::class, 'update'])->name('homepage-images.update');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/support-inquiries', [SupportInquiryController::class, 'index'])->name('support.index');
    Route::post('/support-inquiries/{inquiry}', [SupportInquiryController::class, 'update'])->name('support.update');
    Route::get('/email-deliveries', [AdminEmailDeliveryController::class, 'index'])->name('email-deliveries');
    Route::get('/newsletter-subscribers', [AdminNewsletterSubscriptionController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter-campaigns', [AdminNewsletterCampaignController::class, 'index'])->name('newsletter-campaigns.index');
    Route::post('/newsletter-campaigns', [AdminNewsletterCampaignController::class, 'store'])->name('newsletter-campaigns.store');
    Route::get('/newsletter-campaigns/{campaign}', [AdminNewsletterCampaignController::class, 'show'])->name('newsletter-campaigns.show');
    Route::put('/newsletter-campaigns/{campaign}', [AdminNewsletterCampaignController::class, 'update'])->name('newsletter-campaigns.update');
    Route::post('/newsletter-campaigns/{campaign}/launch', [AdminNewsletterCampaignController::class, 'launch'])
        ->middleware('admin.recent-password')->name('newsletter-campaigns.launch');
    Route::post('/newsletter-campaigns/{campaign}/cancel', [AdminNewsletterCampaignController::class, 'cancel'])
        ->middleware('admin.recent-password')->name('newsletter-campaigns.cancel');
    Route::post('/email-deliveries/{id}/retry', [AdminEmailDeliveryController::class, 'retry'])->name('email-deliveries.retry');
    Route::get('/orders/export', [AdminController::class, 'exportOrders'])->name('orders.export');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/archive', [AdminController::class, 'archiveOrder'])->name('orders.archive');
    Route::post('/orders/{id}/unarchive', [AdminController::class, 'unarchiveOrder'])->name('orders.unarchive');
    Route::delete('/orders/{id}', [AdminController::class, 'trashOrder'])->name('orders.trash');
    Route::post('/orders/{id}/restore', [AdminController::class, 'restoreTrashedOrder'])->name('orders.restore');
    Route::delete('/orders/{id}/purge', [AdminController::class, 'purgeOrder'])->middleware('admin.recent-password')->name('orders.purge');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::post('/orders/{id}/cod-paid', [AdminController::class, 'markCodPaid'])->middleware('admin.recent-password')->name('orders.cod-paid');
    Route::post('/orders/{id}/cancellation-requests/{requestId}/decision', [AdminController::class, 'decideCancellation'])->name('orders.cancellation-decision');
    Route::post('/orders/{id}/return-requests/{requestId}/decision', [OrderReturnController::class, 'decide'])->name('orders.return-decision');
    Route::post('/orders/{id}/return-requests/{requestId}/receive', [OrderReturnController::class, 'receive'])->name('orders.return-receive');
    Route::post('/orders/{id}/return-requests/{requestId}/inspect', [OrderReturnController::class, 'inspect'])->name('orders.return-inspect');
    Route::post('/orders/{id}/return-requests/{requestId}/restock', [OrderReturnController::class, 'restock'])->name('orders.return-restock');
    Route::post('/orders/{id}/return-requests/{requestId}/refund', [OrderRefundController::class, 'approve'])->middleware('admin.recent-password')->name('orders.refund-approve');
    Route::post('/orders/{id}/refunds/{refundId}/verify-recipient', [OrderRefundController::class, 'verifyRecipient'])->middleware('admin.recent-password')->name('orders.refund-verify-recipient');
    Route::post('/orders/{id}/refunds/{refundId}/complete', [OrderRefundController::class, 'complete'])->middleware('admin.recent-password')->name('orders.refund-complete');
    Route::get('/orders/{id}/invoice', [AdminController::class, 'orderInvoice'])->name('orders.invoice');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/stock-levels', [AdminController::class, 'stockLevels'])->name('products.stock-levels');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->middleware('admin.recent-password')->name('products.delete');
    Route::post('/products/{id}/toggle-stock', [AdminController::class, 'toggleStock'])->name('products.toggle-stock');

    // Coupon & Promotional Campaign Management
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
    Route::get('/coupons/create', [AdminController::class, 'couponCreate'])->name('coupons.create');
    Route::post('/coupons', [AdminController::class, 'couponStore'])->name('coupons.store');
    Route::post('/coupons/{id}/toggle', [AdminController::class, 'couponToggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [AdminController::class, 'couponDestroy'])->middleware('admin.recent-password')->name('coupons.destroy');

    // Store and vendor administration
    Route::get('/vendors', [AdminVendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/create', [AdminVendorController::class, 'create'])->name('vendors.create');
    Route::post('/vendors', [AdminVendorController::class, 'store'])->name('vendors.store');
    Route::get('/vendors/{id}/edit', [AdminVendorController::class, 'edit'])->name('vendors.edit');
    Route::put('/vendors/{id}', [AdminVendorController::class, 'update'])->name('vendors.update');
    Route::delete('/vendors/{id}/media/{field}', [AdminVendorController::class, 'deleteMedia'])
        ->whereIn('field', ['logo', 'banner'])->name('vendors.media.delete');
    Route::post('/vendors/{id}/toggle', [AdminVendorController::class, 'toggleActive'])->name('vendors.toggle');
});
