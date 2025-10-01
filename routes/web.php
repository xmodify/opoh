<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;


Route::get('/', function () {
    // return view('dashboard');
     return redirect()->to('web/'); 
});

//Opinsurance หน้าแรก
Route::match(['get','post'],'web', [DashboardController::class, 'index'])->name('web.index');;
