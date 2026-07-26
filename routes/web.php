<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\DashboardController::class, 'search'])->name('search');

    Route::patch('/clients/{client}/archive', [\App\Http\Controllers\ClientController::class, 'archive'])->name('clients.archive');
    Route::resource('clients', \App\Http\Controllers\ClientController::class);

    Route::resource('matters', \App\Http\Controllers\MatterController::class);

    Route::post('matters/{matter}/documents', [\App\Http\Controllers\DocumentController::class, 'store'])->name('matters.documents.store');
    Route::post('matters/{matter}/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('matters.tasks.store');
    Route::post('matters/{matter}/research', [\App\Http\Controllers\ResearchController::class, 'store'])->name('matters.research.store');

    Route::get('documents/{document}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show');
    Route::post('documents/{document}/extract', [\App\Http\Controllers\DocumentController::class, 'extract'])->name('documents.extract');
    Route::post('documents/{document}/analyze', [\App\Http\Controllers\DocumentController::class, 'analyze'])->name('documents.analyze');

    Route::patch('tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
});

require __DIR__.'/auth.php';
