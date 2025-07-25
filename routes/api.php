<?php

use App\Http\Controllers\ShebaController;

Route::post('/sheba', [ShebaController::class, 'store']);
Route::get('/sheba', [ShebaController::class, 'index']);
Route::match(['put', 'post'], '/sheba/{id}', [ShebaController::class, 'update']);
