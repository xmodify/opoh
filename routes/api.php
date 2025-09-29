<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HospitalTokenController;
use App\Http\Controllers\Api\OpInsuranceController;

Route::get('/hospitals/{hospcode}/tokens', [HospitalTokenController::class, 'index']);
Route::post('/hospitals/{hospcode}/tokens', [HospitalTokenController::class, 'issue']);
Route::delete('/hospitals/{hospcode}/tokens/{tokenId}', [HospitalTokenController::class, 'revoke']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/op_insurance', [OpInsuranceController::class, 'ingest']);
});



