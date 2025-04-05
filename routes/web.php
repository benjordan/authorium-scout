<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\EpicController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\ReleaseController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

Route::get('/dashboard', [ReleaseController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

    Route::get('/epics', [EpicController::class, 'index'])->name('epics.index');
    Route::get('/epics/{key}', [EpicController::class, 'show'])->name('epics.show');

    Route::get('/components', [FeatureController::class, 'index'])->name('features.index');
    Route::get('/components/{id}', [FeatureController::class, 'show'])->name('features.show');

    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban');
    Route::get('/kanban/full', [KanbanController::class, 'full'])->name('kanban-full');

    Route::get('/releases', [ReleaseController::class, 'index'])->name('releases.index');
    Route::get('/releases/{id}', [ReleaseController::class, 'show'])->name('releases.show');
    Route::get('/releases/{id}/workload', [ReleaseController::class, 'workload'])->name('releases.workload');
    Route::get('/releases/{releaseKey}/{type}/{status}', [ReleaseController::class, 'statusDetails'])->name('releases.statusDetails');
});

Route::get('/flush-cache', function () {

    // Clear various caches
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return response()->json(['message' => 'Cache cleared successfully.'], 200);
})->name('flush.cache');

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

require __DIR__ . '/auth.php';
