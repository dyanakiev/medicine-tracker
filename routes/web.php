<?php

use App\Http\Controllers\MedicineController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('medicines');
});

Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines');
Route::get('/add-medicine', [MedicineController::class, 'create'])->name('add-medicine');
Route::post('/medicines', [MedicineController::class, 'store'])->name('medicines.store');
Route::get('/medicines/{medicine}/edit', [MedicineController::class, 'edit'])->name('medicines.edit');
Route::post('/medicines/{medicine}/update', [MedicineController::class, 'update'])->name('medicines.update.post');
Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy'])->name('medicines.destroy');
Route::post('/medicines/{medicine}/delete', [MedicineController::class, 'destroy'])->name('medicines.destroy.post');
Route::post('/medicines/{medicine}/taken', [MedicineController::class, 'markTaken'])->name('medicines.taken');
Route::post('/dose-logs/{doseLog}/delete', [MedicineController::class, 'destroyDoseLog'])->name('dose-logs.destroy.post');

Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update.post');
