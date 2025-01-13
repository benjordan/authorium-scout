<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\Auth\GoogleController;

Route::middleware(['auth'])->group(
    function () {
        Route::get('/', [ReleaseController::class, 'unreleasedReleases'])->name('home');
        Route::get('/releases/{releaseKey}', [ReleaseController::class, 'criticalEpics'])->name('release-epics');
        Route::get('/epics/{epicKey}', [ReleaseController::class, 'epicDetails'])->name('epic-details');
        Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban');
        Route::get('/kanban-full', [KanbanController::class, 'full'])->name('kanban-full');
    }
);


Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
