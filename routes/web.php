<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VentaPanelController;

Route::get('/login', [LoginController::class, 'show'])->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout.perform');

Route::middleware('panel.auth')->group(function () {
    Route::get('/', [VentaPanelController::class, 'index'])->name('panel.index');

    Route::get('/ventas/data', [VentaPanelController::class, 'data'])->name('panel.ventas.data');
    Route::get('/ventas/{origen}/{id}', [VentaPanelController::class, 'show'])->name('panel.ventas.show');

    Route::post('/ventas/{origen}/{id}/update', [VentaPanelController::class, 'updateVenta'])->name('panel.ventas.update');
    Route::post('/ventas/{origen}/{id}/fulfillment', [VentaPanelController::class, 'updateFulfillment'])->name('panel.ventas.fulfillment');

    Route::post('/shipments/{origen}/{id}/meta', [VentaPanelController::class, 'updateShipmentMeta'])->name('panel.shipments.meta');
    Route::post('/shipments/{origen}/{id}/status', [VentaPanelController::class, 'updateShipmentStatus'])->name('panel.shipments.status');
});
