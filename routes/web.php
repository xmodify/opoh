<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;

// หน้าแรก redirect ไป web
Route::get('/', function () {
    // return view('dashboard');
     return redirect()->to('web/'); 
});

// หน้า web หลัก
Route::match(['get','post'],'web', [DashboardController::class, 'index'])->name('web.index');;

// Login (สำหรับ Modal login)
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ถ้าคุณต้องการ page /dashboard ให้ล็อกอินก่อน
Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->to('web/'); 
    });
});