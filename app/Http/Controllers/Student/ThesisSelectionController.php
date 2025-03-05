<?php

use Illuminate\Support\Facades\Route;   
use App\Livewire\Student\ThesisSelectionForm;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ThesisTitleController;

// Default route (welcome page)
Route::get('/', ThesisSelectionForm::class)->name('student.thesis-selection');

// Middleware untuk user yang sudah login
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes dengan middleware tambahan
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('students', StudentController::class);
        Route::resource('thesis-titles', ThesisTitleController::class);
        Route::resource('activity-logs', ActivityLogController::class);
    });
});
