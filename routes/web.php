<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\ReleaseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [ReleaseController::class, 'unreleasedReleases'])->name('home');
    Route::get('/releases/{releaseKey}', [ReleaseController::class, 'criticalEpics'])->name('release-epics');
    Route::get('/epics/{epicKey}', [ReleaseController::class, 'epicDetails'])->name('epics.show');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
    Route::get('/features/{id}', [FeatureController::class, 'show'])->name('features.show');

    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban');
    Route::get('/kanban-full', [KanbanController::class, 'full'])->name('kanban-full');
});

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

require __DIR__ . '/auth.php';
