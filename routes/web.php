<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\GuestCvController;

Route::get('/', function () {
    return view('welcome');
})->name('home');
// Public guest CV routes
Route::get('/create-cv', [
    GuestCvController::class,
    'create'
    
])->name('guest-cvs.create');
Route::get('/my-cv/{token}/download', [
    GuestCvController::class,
    'download'
])->middleware('throttle:10,1')
  ->name('guest-cvs.download');
Route::post('/create-cv', [
    GuestCvController::class,
    'store'
])->middleware('throttle:10,1')
  ->name('guest-cvs.store');

Route::get('/my-cv/{token}', [
    GuestCvController::class,
    'preview'
])->name('guest-cvs.preview');
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

    Route::resource('cvs', CvController::class);

    Route::resource('jobs', JobApplicationController::class)
        ->except(['show']);

});

require __DIR__.'/auth.php';
