<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShebaController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/sheba', [ShebaController::class, 'store']);
Route::get('/api/sheba', [ShebaController::class, 'index']);
Route::match(['put', 'post'], '/api/sheba/{id}', [ShebaController::class, 'update']);
