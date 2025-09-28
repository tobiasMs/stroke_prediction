<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StrokePredictionController;

Route::get('/', [StrokePredictionController::class, 'showForm']);
Route::post('/predict', [StrokePredictionController::class, 'predict']);