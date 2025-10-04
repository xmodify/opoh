<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HospitalTokenController;
use App\Http\Controllers\Api\OpInsuranceController;
use App\Http\Controllers\Api\OpdController;
use App\Http\Controllers\Api\IpdController;

Route::get('/hospitals/{hospcode}/tokens', [HospitalTokenController::class, 'index']);
Route::post('/hospitals/{hospcode}/tokens', [HospitalTokenController::class, 'issue']);
    // {
    // "name": "10987-ingest",
    // "abilities": ["ingest"]
    // }
Route::delete('/hospitals/{hospcode}/tokens/{tokenId}', [HospitalTokenController::class, 'revoke']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/op_insurance', [OpInsuranceController::class, 'ingest']);
    Route::post('/opd', [OpdController::class, 'opd']);
    Route::post('/ipd', [IpdController::class, 'ipd']);
});



