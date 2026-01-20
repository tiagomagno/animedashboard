<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\RankingController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
Route::get('/anime/{anime}', [DashboardController::class, 'show'])->name('dashboard.show');

// Seasons Management
Route::prefix('seasons')->name('seasons.')->group(function () {
    Route::get('/', [SeasonController::class, 'index'])->name('index');
    Route::post('/import', [SeasonController::class, 'import'])->name('import');
    Route::post('/import-year', [SeasonController::class, 'importYear'])->name('import-year');
    Route::post('/{season}/update-stats', [SeasonController::class, 'updateStats'])->name('update-stats');
    Route::delete('/{season}', [SeasonController::class, 'destroy'])->name('destroy');
});

// Reviews
Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/anime/{anime}/create', [ReviewController::class, 'create'])->name('create');
    Route::post('/anime/{anime}', [ReviewController::class, 'store'])->name('store');
    Route::delete('/anime/{anime}', [ReviewController::class, 'destroy'])->name('destroy');
});
