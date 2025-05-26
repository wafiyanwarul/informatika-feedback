<?php

use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Maintenance routes - only accessible by authenticated admin users
Route::middleware(['auth', 'admin'])->prefix('admin/maintenance')->group(function () {
    Route::post('/toggle', [MaintenanceController::class, 'toggle'])->name('maintenance.toggle');
    Route::get('/status', [MaintenanceController::class, 'status'])->name('maintenance.status');
});
