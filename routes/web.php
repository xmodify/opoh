<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;


Route::get('/', function () {
    return view('welcome');
});

//Opinsurance หน้าแรก
Route::get('op_insurance/dashboard', [DashboardController::class, 'index'])->name('opinsurance.dashboard');
