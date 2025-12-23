<?php

use App\Livewire\MedicineForm;
use App\Livewire\MedicineList;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('medicines');
});

Route::get('/medicines', MedicineList::class)->name('medicines');

Route::get('/add-medicine', MedicineForm::class)->name('add-medicine');

Route::get('/settings', Settings::class)->name('settings');
