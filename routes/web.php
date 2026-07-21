<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorPlanController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LightingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MasterDataController;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'showLoginForm'])->name('signin');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/signin', [AuthController::class, 'login'])->name('signin.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Maintenance (Accessible by both Admin and Teknisi)
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::put('/maintenance/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::put('/maintenance/{maintenance}/work', [MaintenanceController::class, 'work'])->name('maintenance.work');
    Route::post('/maintenance/{maintenance}/approve', [MaintenanceController::class, 'approve'])->name('maintenance.approve');
    Route::delete('/maintenance/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::patch('/maintenance/{maintenance}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.status');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Floor Plan
        Route::get('/floor-plan', [FloorPlanController::class, 'index'])->name('floor-plan');
        Route::get('/floor-plan/data', [FloorPlanController::class, 'getFloorData'])->name('floor-plan.data');
        Route::post('/floor-plan/lamp', [FloorPlanController::class, 'saveLamp'])->name('floor-plan.lamp.save');
        Route::put('/floor-plan/lamp/{id}/position', [FloorPlanController::class, 'updateLampPosition'])->name('floor-plan.lamp.position');
        Route::patch('/floor-plan/lamp/{id}/status', [FloorPlanController::class, 'updateLampStatus'])->name('floor-plan.lamp.status');
        Route::put('/floor-plan/lamp/{id}/rotation', [FloorPlanController::class, 'updateLampRotation'])->name('floor-plan.lamp.rotation');
        Route::put('/floor-plan/lamp/{id}/dimensions', [FloorPlanController::class, 'updateLampDimensions'])->name('floor-plan.lamp.dimensions');
        Route::delete('/floor-plan/lamp/{id}', [FloorPlanController::class, 'deleteLamp'])->name('floor-plan.lamp.delete');
        Route::post('/floor-plan/{floorId}/upload', [FloorPlanController::class, 'uploadFloorPlan'])->name('floor-plan.upload');

        // Lighting
        Route::get('/lighting', function () {
            return redirect()->route('inventory', ['tab' => 'stock']);
        })->name('lighting');
        Route::post('/lighting', [LightingController::class, 'store'])->name('lighting.store');
        Route::put('/lighting/{lampType}', [LightingController::class, 'update'])->name('lighting.update');
        Route::delete('/lighting/{lampType}', [LightingController::class, 'destroy'])->name('lighting.destroy');

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::post('/inventory/lamp-type', [InventoryController::class, 'storeLampType'])->name('inventory.lamp-type.store');
        Route::put('/inventory/lamp-type/{lampType}', [InventoryController::class, 'updateLampType'])->name('inventory.lamp-type.update');
        Route::delete('/inventory/lamp-type/{lampType}', [InventoryController::class, 'destroyLampType'])->name('inventory.lamp-type.destroy');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Master Data
        Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data');
        Route::post('/master-data/building', [MasterDataController::class, 'storeBuilding'])->name('master-data.building.store');
        Route::put('/master-data/building/{building}', [MasterDataController::class, 'updateBuilding'])->name('master-data.building.update');
        Route::delete('/master-data/building/{building}', [MasterDataController::class, 'destroyBuilding'])->name('master-data.building.destroy');

        Route::post('/master-data/floor', [MasterDataController::class, 'storeFloor'])->name('master-data.floor.store');
        Route::put('/master-data/floor/{floor}', [MasterDataController::class, 'updateFloor'])->name('master-data.floor.update');
        Route::delete('/master-data/floor/{floor}', [MasterDataController::class, 'destroyFloor'])->name('master-data.floor.destroy');

        Route::post('/master-data/room', [MasterDataController::class, 'storeRoom'])->name('master-data.room.store');
        Route::put('/master-data/room/{room}', [MasterDataController::class, 'updateRoom'])->name('master-data.room.update');
        Route::delete('/master-data/room/{room}', [MasterDataController::class, 'destroyRoom'])->name('master-data.room.destroy');
    });
});

// Error 404
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');
