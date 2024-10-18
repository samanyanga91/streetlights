<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StreetlightController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return view('home');
})->middleware(['auth'])->name('home');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/settings', [RankingController::class, 'settings'])->name('settings');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

});

Route::middleware(['auth', 'technician'])->group(function () {
    Route::get('/streetlights', function () {
        return view('streetlights');
    })->name('streetlights');
    Route::post('/streetlights', [StreetlightController::class, 'updateStreetlight'])->name('update.streetlight');

});


Route::middleware('auth')->group(function () {
    Route::get('/request', function () {
        return view('request-form');
    })->name('requests');
    //Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'password'])->name('password.update');
    Route::post('/request', [StreetlightController::class, 'postRequest'])->name('post.request');
});





require __DIR__.'/auth.php';
require __DIR__.'/api.php';
