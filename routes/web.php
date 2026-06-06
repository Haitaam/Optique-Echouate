<?php

use App\Features\Pages\Controllers\PageController;
use App\Features\Pages\Controllers\LanguageController;
use App\Features\Products\Controllers\ProductController;
use App\Features\Quiz\Controllers\QuizController;
use App\Features\Appointments\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/avis', [PageController::class, 'storeAvis'])->name('avis.store');
Route::get('/eye-health', [PageController::class, 'eyeHealth'])->name('eye-health');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');
Route::get('/products/search-json', [ProductController::class, 'searchJson'])->name('products.search-json');

Route::get('/quiz', [QuizController::class, 'index'])->name('quiz');
Route::post('/quiz/analyze', [QuizController::class, 'analyze'])->name('quiz.analyze');

Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

Route::get('/live/hash', [App\Http\Controllers\LiveController::class, 'hash'])->name('live.hash');
Route::get('/live/product/{id}', [App\Http\Controllers\LiveController::class, 'product'])->name('live.product');
Route::get('/live/order/{id}', [App\Http\Controllers\LiveController::class, 'order'])->name('live.order');
Route::get('/live/admin', [App\Http\Controllers\LiveController::class, 'admin'])->name('live.admin');
Route::get('/live/settings', [App\Http\Controllers\LiveController::class, 'settings'])->name('live.settings');

Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/password/reset', function () { return view('pages.auth.passwords.email'); })->name('password.request');

Route::middleware('auth:customer')->group(function () {
    Route::get('/account', [App\Http\Controllers\AccountController::class, 'index'])->name('profile');
    Route::get('/account/orders', [App\Http\Controllers\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order}', [App\Http\Controllers\AccountController::class, 'orderDetail'])->name('account.orders.show');
    Route::get('/account/orders/{order}/items', [App\Http\Controllers\AccountController::class, 'orderItems'])->name('account.orders.items');
    Route::get('/account/reviews', [App\Http\Controllers\AccountController::class, 'reviews'])->name('account.reviews');
    Route::post('/account/reviews', [App\Http\Controllers\AccountController::class, 'storeReview'])->name('account.reviews.store');
    Route::get('/account/addresses', [App\Http\Controllers\AccountController::class, 'addresses'])->name('account.addresses');
    Route::get('/account/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('account.wishlist');
    Route::post('/wishlist', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/suivre-commande', [App\Http\Controllers\TrackingController::class, '__invoke'])->name('order.tracking');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/glasses', [App\Http\Controllers\Admin\GlassesController::class, 'index'])->name('glasses.index');
        Route::get('/glasses/create', [App\Http\Controllers\Admin\GlassesController::class, 'create'])->name('glasses.create');
        Route::post('/glasses/upload', [App\Http\Controllers\Admin\GlassesController::class, 'upload'])->name('glasses.upload');
        Route::get('/glasses/{product}/edit', [App\Http\Controllers\Admin\GlassesController::class, 'edit'])->name('glasses.edit');
        Route::put('/glasses/{product}', [App\Http\Controllers\Admin\GlassesController::class, 'update'])->name('glasses.update');
        Route::delete('/glasses/{product}', [App\Http\Controllers\Admin\GlassesController::class, 'destroy'])->name('glasses.destroy');
        Route::post('/glasses/sync', [App\Http\Controllers\Admin\GlassesController::class, 'sync'])->name('glasses.sync');
        Route::post('/glasses/bulk-delete', [App\Http\Controllers\Admin\GlassesController::class, 'bulkDelete'])->name('glasses.bulk-delete');
        Route::post('/glasses/bulk-update', [App\Http\Controllers\Admin\GlassesController::class, 'bulkUpdate'])->name('glasses.bulk-update');
        Route::post('/glasses/bulk-sync-fix', [App\Http\Controllers\Admin\GlassesController::class, 'bulkSyncFix'])->name('glasses.bulk-sync-fix');
        Route::post('/glasses/smart-fix', [App\Http\Controllers\Admin\GlassesController::class, 'smartFix'])->name('glasses.smart-fix');
        Route::get('/glasses/export', [App\Http\Controllers\Admin\GlassesController::class, 'export'])->name('glasses.export');

        Route::get('/testimonials', [App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('testimonials.index');
        Route::get('/testimonials/create', [App\Http\Controllers\Admin\TestimonialController::class, 'create'])->name('testimonials.create');
        Route::post('/testimonials', [App\Http\Controllers\Admin\TestimonialController::class, 'store'])->name('testimonials.store');
        Route::get('/testimonials/{testimonial}/edit', [App\Http\Controllers\Admin\TestimonialController::class, 'edit'])->name('testimonials.edit');
        Route::put('/testimonials/{testimonial}', [App\Http\Controllers\Admin\TestimonialController::class, 'update'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy');
        Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/pending', [App\Http\Controllers\Admin\OrderController::class, 'pending'])->name('orders.pending');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/{order}/confirm', [App\Http\Controllers\Admin\OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('/orders/{order}/cancel', [App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{order}/whatsapp', [App\Http\Controllers\Admin\OrderController::class, 'sendWhatsApp'])->name('orders.whatsapp');
        Route::delete('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');

        Route::get('/brands', [App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [App\Http\Controllers\Admin\BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit', [App\Http\Controllers\Admin\BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('brands.destroy');

        Route::get('/colors', [App\Http\Controllers\Admin\ColorController::class, 'index'])->name('colors.index');
        Route::get('/colors/create', [App\Http\Controllers\Admin\ColorController::class, 'create'])->name('colors.create');
        Route::post('/colors', [App\Http\Controllers\Admin\ColorController::class, 'store'])->name('colors.store');
        Route::get('/colors/{color}/edit', [App\Http\Controllers\Admin\ColorController::class, 'edit'])->name('colors.edit');
        Route::put('/colors/{color}', [App\Http\Controllers\Admin\ColorController::class, 'update'])->name('colors.update');
        Route::delete('/colors/{color}', [App\Http\Controllers\Admin\ColorController::class, 'destroy'])->name('colors.destroy');

        Route::get('/shapes', [App\Http\Controllers\Admin\ShapeController::class, 'index'])->name('shapes.index');
        Route::get('/shapes/create', [App\Http\Controllers\Admin\ShapeController::class, 'create'])->name('shapes.create');
        Route::post('/shapes', [App\Http\Controllers\Admin\ShapeController::class, 'store'])->name('shapes.store');
        Route::get('/shapes/{shape}/edit', [App\Http\Controllers\Admin\ShapeController::class, 'edit'])->name('shapes.edit');
        Route::put('/shapes/{shape}', [App\Http\Controllers\Admin\ShapeController::class, 'update'])->name('shapes.update');
        Route::delete('/shapes/{shape}', [App\Http\Controllers\Admin\ShapeController::class, 'destroy'])->name('shapes.destroy');

        Route::get('/genders', [App\Http\Controllers\Admin\GenderController::class, 'index'])->name('genders.index');
        Route::get('/genders/create', [App\Http\Controllers\Admin\GenderController::class, 'create'])->name('genders.create');
        Route::post('/genders', [App\Http\Controllers\Admin\GenderController::class, 'store'])->name('genders.store');
        Route::get('/genders/{gender}/edit', [App\Http\Controllers\Admin\GenderController::class, 'edit'])->name('genders.edit');
        Route::put('/genders/{gender}', [App\Http\Controllers\Admin\GenderController::class, 'update'])->name('genders.update');
        Route::delete('/genders/{gender}', [App\Http\Controllers\Admin\GenderController::class, 'destroy'])->name('genders.destroy');

        Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        Route::delete('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [App\Http\Controllers\Admin\NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/notifications/{notification}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('notifications.read');
        Route::get('/notifications/json', [App\Http\Controllers\Admin\NotificationController::class, 'json'])->name('notifications.json');

        Route::get('/quiz-answers', [App\Http\Controllers\Admin\QuizAnswerController::class, 'index'])->name('quiz-answers.index');

        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
