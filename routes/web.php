<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\GatePointController;
use App\Http\Controllers\AlertController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('vehicles', VehicleController::class);
        Route::get('vehicles/{vehicle}/qr-download', [VehicleController::class, 'downloadQr'])->name('vehicles.qr.download');
        Route::get('vehicles/{vehicle}/qr-print', [VehicleController::class, 'printQr'])->name('vehicles.qr.print');
        Route::post('vehicles/{vehicle}/qr-reissue', [VehicleController::class, 'reissueQr'])->name('vehicles.qr.reissue');
        Route::post('vehicles/{vehicle}/qr-email', [VehicleController::class, 'emailQr'])->name('vehicles.qr.email');
        Route::get('logs', [AccessLogController::class, 'index'])->name('logs.index');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::resource('users', RegisteredUserController::class);
        Route::get('gates', [GatePointController::class, 'index'])->name('gates.index');
        Route::post('gates', [GatePointController::class, 'store'])->name('gates.store');
        Route::put('gates/{gate}', [GatePointController::class, 'update'])->name('gates.update');
        Route::delete('gates/{gate}', [GatePointController::class, 'destroy'])->name('gates.destroy');
        Route::get('alerts', [AlertController::class, 'index'])->name('alerts.index');
        Route::post('alerts/{log}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');
        Route::get('alerts/count', [AlertController::class, 'unacknowledgedCount'])->name('alerts.count');
    });

    Route::middleware('role:officer')->prefix('officer')->name('officer.')->group(function () {
        Route::get('/scan', [ScanController::class, 'screen'])->name('scan');
        Route::post('/verify', [ScanController::class, 'verify'])->name('verify');
    });
});

require __DIR__.'/auth.php';
