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

Route::get('/quiz', [QuizController::class, 'index'])->name('quiz');
Route::post('/quiz/analyze', [QuizController::class, 'analyze'])->name('quiz.analyze');

Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

Route::prefix('admin')->name('admin.')->group(function () {
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
});
