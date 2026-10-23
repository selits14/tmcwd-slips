<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GasSlipController;
use App\Http\Controllers\GatePassController;
use App\Http\Controllers\PassSlipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // All staff can view slips; only encoders create them.
    Route::resource('pass-slips', PassSlipController::class)
        ->only(['create', 'store'])
        ->middleware('role:super_admin|encoder');
    Route::resource('pass-slips', PassSlipController::class)
        ->only(['index', 'show'])
        ->middleware('role:super_admin|approver|encoder|driver|guard|viewer');
    Route::post('pass-slips/{passSlip}/approve',  [PassSlipController::class, 'approve'])->name('pass-slips.approve');
    Route::post('pass-slips/{passSlip}/reject',   [PassSlipController::class, 'reject'])->name('pass-slips.reject');
    Route::post('pass-slips/{passSlip}/time-out', [PassSlipController::class, 'timeOut'])->middleware('role:super_admin|guard')->name('pass-slips.time-out');
    Route::post('pass-slips/{passSlip}/time-in',  [PassSlipController::class, 'timeIn'])->middleware('role:super_admin|guard')->name('pass-slips.time-in');
    Route::get('pass-slips/{passSlip}/print',     [PassSlipController::class, 'print'])->middleware('role:super_admin|approver|encoder|driver|guard|viewer')->name('pass-slips.print');

    // All staff can view passes; only encoders create them.
    Route::resource('gate-passes', GatePassController::class)
        ->only(['create', 'store'])
        ->middleware('role:super_admin|encoder');
    Route::resource('gate-passes', GatePassController::class)
        ->only(['index', 'show'])
        ->middleware('role:super_admin|approver|encoder|driver|guard|viewer');
    Route::post('gate-passes/{gatePass}/approve',  [GatePassController::class, 'approve'])->name('gate-passes.approve');
    Route::post('gate-passes/{gatePass}/reject',   [GatePassController::class, 'reject'])->name('gate-passes.reject');
    Route::post('gate-passes/{gatePass}/release',  [GatePassController::class, 'release'])->middleware('role:super_admin|guard')->name('gate-passes.release');
    Route::post('gate-passes/{gatePass}/returned', [GatePassController::class, 'returned'])->middleware('role:super_admin|guard')->name('gate-passes.returned');
    Route::get('gate-passes/{gatePass}/print',     [GatePassController::class, 'print'])->middleware('role:super_admin|approver|encoder|driver|guard|viewer')->name('gate-passes.print');

    // All staff can view gas slips; only encoders create them.
    Route::resource('gas-slips', GasSlipController::class)
        ->only(['create', 'store'])
        ->middleware('role:super_admin|encoder');
    Route::resource('gas-slips', GasSlipController::class)
        ->only(['index', 'show'])
        ->middleware('role:super_admin|approver|encoder|driver|guard|viewer');
    Route::post('gas-slips/{gasSlip}/approve',  [GasSlipController::class, 'approve'])->name('gas-slips.approve');
    Route::post('gas-slips/{gasSlip}/reject',   [GasSlipController::class, 'reject'])->name('gas-slips.reject');
    Route::post('gas-slips/{gasSlip}/complete', [GasSlipController::class, 'complete'])->middleware('role:super_admin|driver')->name('gas-slips.complete');
    Route::get('gas-slips/{gasSlip}/print',     [GasSlipController::class, 'print'])->middleware('role:super_admin|approver|encoder|driver|guard|viewer')->name('gas-slips.print');

    // Reports
Route::prefix('reports')->name('reports.')->middleware('role:super_admin|approver|encoder|viewer')->group(function () {
    Route::get('/',                           [ReportController::class, 'index'])->name('index');
    Route::get('/pass-slips',                 [ReportController::class, 'passSlips'])->name('pass-slips');
    Route::get('/gate-passes',                [ReportController::class, 'gatePasses'])->name('gate-passes');
    Route::get('/fuel-consumption',           [ReportController::class, 'fuelConsumption'])->name('fuel-consumption');
    Route::get('/fuel-consumption/print',     [ReportController::class, 'fuelConsumptionPrint'])->name('fuel-consumption.print');

    // New individual vehicle reports
    Route::get('/vehicle-mrot',               [ReportController::class, 'vehicleMrot'])->name('vehicle-mrot');
    Route::get('/vehicle-mrot/print',         [ReportController::class, 'vehicleMrotPrint'])->name('vehicle-mrot.print');
    Route::get('/vehicle-rfc',                [ReportController::class, 'vehicleRfc'])->name('vehicle-rfc');
    Route::get('/vehicle-rfc/print',          [ReportController::class, 'vehicleRfcPrint'])->name('vehicle-rfc.print');
});

        // Batch print routes
    Route::post('pass-slips/batch-print',  [PassSlipController::class, 'batchPrint'])->middleware('role:super_admin|approver|encoder|driver|guard|viewer')->name('pass-slips.batch-print');
    Route::post('gate-passes/batch-print', [GatePassController::class, 'batchPrint'])->middleware('role:super_admin|approver|encoder|driver|guard|viewer')->name('gate-passes.batch-print');
    Route::post('gas-slips/batch-print',   [GasSlipController::class, 'batchPrint'])->middleware('role:super_admin|approver|encoder|driver|guard|viewer')->name('gas-slips.batch-print');
    // Admin
   Route::prefix('admin')->name('admin.')->middleware('role:super_admin')->group(function () {
    Route::resource('users',     \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::resource('divisions', \App\Http\Controllers\DivisionController::class)->except(['show']);
    Route::resource('vehicles',  \App\Http\Controllers\Admin\VehicleController::class)->except(['show']);
}); // ← admin group closes here

}); // ← auth group closes here

require __DIR__.'/auth.php';
