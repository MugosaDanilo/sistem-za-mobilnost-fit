<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MobilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniverzitetController;


Route::get('/', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match ((int)$user->type) {
        0 => redirect()->route('adminDashboardShow'),
        1 => redirect()->route('profesorDashboardShow'),
    };
})->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('adminAuth')->prefix('admin')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('adminDashboardShow');

    Route::get('/mobilnost', [MobilityController::class, 'index'])->name('admin.mobility');
    Route::post('/mobilnost', [MobilityController::class, 'upload'])->name('admin.mobility.upload');
    Route::post('/mobilnost/export', [MobilityController::class, 'export'])->name('admin.mobility.export');
    Route::post('/mobility/save', [MobilityController::class, 'save'])->name('admin.mobility.save');

    Route::get('/users/', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

 
    Route::get('/univerzitet', [UniverzitetController::class, 'index'])->name('univerzitet.index');
    Route::get('/univerzitet/create', [UniverzitetController::class, 'create'])->name('univerzitet.create');
    Route::post('/univerzitet', [UniverzitetController::class, 'store'])->name('univerzitet.store');
    Route::get('/univerzitet/{id}/edit', [UniverzitetController::class, 'edit'])->name('univerzitet.edit');
    Route::put('/univerzitet/{id}', [UniverzitetController::class, 'update'])->name('univerzitet.update');
    Route::delete('/univerzitet/{id}', [UniverzitetController::class, 'destroy'])->name('univerzitet.destroy');

    Route::resource('prepisi', \App\Http\Controllers\PrepisController::class)->names('prepis');
});

Route::middleware('profesorAuth')->prefix('profesor')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'profesorDashboard'])->name('profesorDashboardShow');

    Route::get('/mobilnost', [MobilityController::class, 'index'])->name('profesor.mobility');
    Route::post('/mobilnost', [MobilityController::class, 'upload'])->name('profesor.mobility.upload');
    Route::post('/mobilnost/export', [MobilityController::class, 'export'])->name('profesor.mobility.export');
    Route::post('/mobility/save', [MobilityController::class, 'save'])->name('profesor.mobility.save');
});

require __DIR__.'/auth.php';
