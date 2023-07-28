<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientControllerAPI;
use Illuminate\Support\Facades\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('guest')->group( function() {
    Route::post('register', [PatientControllerAPI::class, 'register'])->name('patient.register'); // test
    Route::post('login', [PatientControllerAPI::class, 'login'])->name('patient.login'); // test
});

Route::middleware('auth:apipatient')->group( function() {
    Route::put('patient/edit', [PatientControllerAPI::class, 'edit'])->name('patient.edit'); // test
    Route::post('patient', [PatientControllerAPI::class, 'logout'])->name('patient.logout');
});

Route::middleware('auth:webdoctor')->group( function() {
    Route::get('doctor', [DoctorController::class, 'index'])->name('doctor.dashboard');
    Route::post('doctor', [DoctorController::class, 'handleLogout'])->name('doctor.handleLogout');
});