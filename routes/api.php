<?php

use App\Http\Controllers\ShebaController;

Route::get('/sheba', [ShebaController::class, 'index']);
Route::post('/sheba', [ShebaController::class, 'store']);
Route::match(['put', 'post'], '/sheba/{id}', [ShebaController::class, 'update']);
