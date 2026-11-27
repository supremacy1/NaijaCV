<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\GuestCvController;
use App\Http\Controllers\AiCvAssistantController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// ========================================
// PUBLIC GUEST CV ROUTES
// No login required
// ========================================

Route::get('/create-cv', [
    GuestCvController::class,
    'create'
])->name('guest-cvs.create');

Route::post('/create-cv', [
    GuestCvController::class,
    'store'
])->middleware('throttle:10,1')
  ->name('guest-cvs.store');

Route::get('/my-cv/{token}', [
    GuestCvController::class,
    'preview'
])->name('guest-cvs.preview');

Route::get('/my-cv/{token}/save', [
    GuestCvController::class,
    'beginSave'
])->name('guest-cvs.save-start');

Route::get('/my-cv/{token}/download', [
    GuestCvController::class,
    'download'
])->middleware('throttle:10,1')
  ->name('guest-cvs.download');

// ========================================
// PUBLIC PAYSTACK ROUTES
// No login required
// ========================================

Route::post('/my-cv/{token}/pay', [
    GuestCvController::class,
    'pay'
])->middleware('throttle:5,1')
  ->name('guest-cvs.pay');

Route::get('/my-cv/{token}/payment/callback', [
    GuestCvController::class,
    'paymentCallback'
])->middleware('throttle:10,1')
  ->name('guest-cvs.payment.callback');
// Paystack webhook: no login required
Route::post('/paystack/webhook', [
    GuestCvController::class,
    'webhook'
])->name('paystack.webhook');

// Customer payment recovery: no login required
Route::get('/my-cv/{token}/recover-payment', [
    GuestCvController::class,
    'recoverPayment'
])->middleware('throttle:10,1')
  ->name('guest-cvs.recover');

Route::post('/my-cv/{token}/save', [
    GuestCvController::class,
    'saveToAccount'
])->middleware('auth')
  ->name('guest-cvs.save');

// Keep your existing payment and callback routes public too.
// ========================================
// AUTHENTICATED USER ROUTES
// ========================================

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/cvs/{cv}/download', [CvController::class, 'download'])
        ->name('cvs.download');

    Route::post('/cvs/{cv}/pay', [CvController::class, 'pay'])
        ->middleware('throttle:5,1')
        ->name('cvs.pay');

    Route::get('/cvs/{cv}/payment/callback', [CvController::class, 'paymentCallback'])
        ->name('cvs.payment.callback');

    Route::post('/cvs/preview', [CvController::class, 'previewDraft'])
        ->name('cvs.preview.create');

    Route::get('/cvs/preview/{draft}', [CvController::class, 'showDraftPreview'])
        ->name('cvs.preview');

    Route::post('/cvs/preview/{draft}/edit', [CvController::class, 'editDraft'])
        ->name('cvs.preview.edit');

    Route::post('/cvs/preview/{draft}/save', [CvController::class, 'saveDraft'])
        ->name('cvs.preview.save');

    Route::post('/cvs/preview/{draft}/download', [CvController::class, 'downloadDraft'])
        ->name('cvs.preview.download');

    Route::post('/cvs/preview/{draft}/pay', [CvController::class, 'payDraft'])
        ->middleware('throttle:5,1')
        ->name('cvs.preview.pay');

    Route::resource('cvs', CvController::class);

    Route::resource('jobs', JobApplicationController::class)
        ->except(['show']);
});
    Route::resource('cvs', CvController::class);

    Route::resource('jobs', JobApplicationController::class)
        ->except(['show']);

});
Route::post('/ai/cv-assist', [AiCvAssistantController::class, 'generate'])
    ->middleware('throttle:10,1')
    ->name('ai.cv-assist');
require __DIR__.'/auth.php';
