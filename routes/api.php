<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group( function() {
    Route::get('/', [DashboardController::class, 'index_api'])->name('home');

    Route::get('register', [PatientController::class, 'register_api'])->name('patient.register'); // done
    Route::post('register', [PatientController::class, 'handleRegister_api'])->name('patient.handleRegister'); // done
    
    Route::get('login', [PatientController::class, 'login'])->name('login');
    Route::post('login', [PatientController::class, 'handleLogin'])->name('patient.handleLogin');
    Route::post('doctor/login', [DoctorController::class, 'handleLogin'])->name('doctor.handleLogin');
});

Route::middleware('auth:web')->group( function() {
    Route::get('patient', [PatientController::class, 'index'])->name('patient.dashboard');
    Route::get('patient/edit', [PatientController::class, 'edit'])->name('patient.edit');
    Route::put('patient/edit', [PatientController::class, 'handleEdit'])->name('patient.handleEdit');
    Route::post('patient', [PatientController::class, 'handleLogout'])->name('patient.handleLogout');
});

Route::middleware('auth:webdoctor')->group( function() {
    Route::get('doctor', [DoctorController::class, 'index'])->name('doctor.dashboard');
    Route::post('doctor', [DoctorController::class, 'handleLogout'])->name('doctor.handleLogout');
});