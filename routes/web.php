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

    return match ((int) $user->type) {
        0 => redirect()->route('adminDashboardShow'),
        1 => redirect()->route('profesorDashboardShow'),
    };
})->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('adminAuth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('adminDashboardShow');

    Route::get('/mobilnost', [MobilityController::class, 'index'])->name('admin.mobility');
    Route::post('/mobilnost', [MobilityController::class, 'upload'])->name('admin.mobility.upload');
    Route::post('/mobilnost/export', [MobilityController::class, 'export'])->name('admin.mobility.export');
    Route::post('/mobility/save', [MobilityController::class, 'save'])->name('admin.mobility.save');
    Route::get('/mobility/student-subjects', [MobilityController::class, 'getStudentSubjects'])->name('admin.mobility.student-subjects');
    Route::get('/mobility/faculty-subjects', [MobilityController::class, 'getFacultySubjects'])->name('admin.mobility.faculty-subjects');
    Route::get('/mobility/{id}', [MobilityController::class, 'show'])->name('admin.mobility.show');
    Route::post('/mobility/grade/{id}', [MobilityController::class, 'updateGrade'])->name('admin.mobility.update-grade');
    Route::post('/mobility/{id}/grades', [MobilityController::class, 'updateGrades'])->name('admin.mobility.update-grades');
    Route::post('/mobility/{id}/export-word', [MobilityController::class, 'exportWord'])->name('admin.mobility.export-word');
    Route::post('/mobility/{id}/lock', [MobilityController::class, 'lock'])->name('admin.mobility.lock');
    Route::delete('/mobilnost/{id}', [MobilityController::class, 'destroy'])->name('admin.mobility.destroy');

    Route::get('/users/', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/students', [App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [App\Http\Controllers\StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}/edit', [App\Http\Controllers\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [App\Http\Controllers\StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');


    Route::get('/univerzitet', [UniverzitetController::class, 'index'])->name('univerzitet.index');
    Route::get('/univerzitet/create', [UniverzitetController::class, 'create'])->name('univerzitet.create');
    Route::post('/univerzitet', [UniverzitetController::class, 'store'])->name('univerzitet.store');
    Route::get('/univerzitet/{id}/edit', [UniverzitetController::class, 'edit'])->name('univerzitet.edit');
    Route::put('/univerzitet/{id}', [UniverzitetController::class, 'update'])->name('univerzitet.update');
    Route::delete('/univerzitet/{id}', [UniverzitetController::class, 'destroy'])->name('univerzitet.destroy');

    Route::get('/prepisi/professor-match', [\App\Http\Controllers\PrepisController::class, 'professorMatch'])->name('prepis.professor-match');
    Route::resource('prepisi', \App\Http\Controllers\PrepisController::class)->names('prepis');

    // Izvjestaji (reports) - yearly statistics
    Route::get('/izvjestaji', [\App\Http\Controllers\IzvjestajiController::class, 'index'])->name('izvjestaji.index');
    Route::get('/izvjestaji/export/{type}', [\App\Http\Controllers\IzvjestajiController::class, 'export'])->name('izvjestaji.export');

    Route::get('/fakulteti', [\App\Http\Controllers\FakultetController::class, 'index'])->name('fakulteti.index');
    Route::post('/fakulteti', [\App\Http\Controllers\FakultetController::class, 'store'])->name('fakulteti.store');
    Route::put('/fakulteti/{id}', [\App\Http\Controllers\FakultetController::class, 'update'])->name('fakulteti.update');
    Route::delete('/fakulteti/{id}', [\App\Http\Controllers\FakultetController::class, 'destroy'])->name('fakulteti.destroy');

    Route::get('/fakulteti/{fakultet}/predmeti', [\App\Http\Controllers\PredmetController::class, 'index'])->name('fakulteti.predmeti.index');

    Route::get('/users/{id}/subjects', [App\Http\Controllers\ProfesorPredmetController::class, 'index'])->name('users.subjects.index');
    Route::post('/users/{id}/subjects', [App\Http\Controllers\ProfesorPredmetController::class, 'store'])->name('users.subjects.store');
    Route::delete('/users/{id}/subjects/{predmet_id}', [App\Http\Controllers\ProfesorPredmetController::class, 'destroy'])->name('users.subjects.destroy');

    Route::post('/predmeti', [\App\Http\Controllers\PredmetController::class, 'store'])->name('predmeti.store');
    Route::put('/predmeti/{id}', [\App\Http\Controllers\PredmetController::class, 'update'])->name('predmeti.update');
    Route::delete('/predmeti/{id}', [\App\Http\Controllers\PredmetController::class, 'destroy'])->name('predmeti.destroy');
});

Route::middleware('profesorAuth')->prefix('profesor')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'profesorDashboard'])->name('profesorDashboardShow');

    Route::get('/mobilnost', [MobilityController::class, 'index'])->name('profesor.mobility');
    Route::post('/mobilnost', [MobilityController::class, 'upload'])->name('profesor.mobility.upload');
    Route::post('/mobilnost/export', [MobilityController::class, 'export'])->name('profesor.mobility.export');
    Route::post('/mobility/save', [MobilityController::class, 'save'])->name('profesor.mobility.save');

    Route::post('/prepis-agreement/{id}/accept', [App\Http\Controllers\PrepisAgreementController::class, 'accept'])->name('prepis-agreement.accept');
    Route::post('/prepis-agreement/{id}/reject', [App\Http\Controllers\PrepisAgreementController::class, 'reject'])->name('prepis-agreement.reject');
});

require __DIR__ . '/auth.php';
