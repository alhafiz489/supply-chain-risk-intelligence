<?php

use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PortController;
use Illuminate\Support\Facades\Route;

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

Route::get('/ports', [PortController::class, 'index']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/currency', [CurrencyController::class, 'show']);