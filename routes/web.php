<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\HelpChatController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/terms', 'terms')->name('terms');
    Route::get('/accessibility', 'accessibility')->name('accessibility');
    Route::get('/community-map', 'communityMap')->name('community-map');
    Route::get('/help', 'help')->name('help');
});

Route::post('/help/chat', HelpChatController::class)
    ->middleware('throttle:20,1')
    ->name('help.chat');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    Route::resource('reports', ReportController::class)
        ->only(['index', 'create', 'store', 'show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', EnsureUserIsAdmin::class])
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        Route::controller(AdminReportController::class)
            ->prefix('reports')
            ->name('reports.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{report}', 'show')->name('show');
                Route::patch('/{report}/status', 'updateStatus')->name('status.update');
            });
    });

require __DIR__.'/auth.php';
