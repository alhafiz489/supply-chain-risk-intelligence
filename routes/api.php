<?php

use App\Http\Controllers\Api\EconomicController;
use App\Http\Middleware\LogApiRequest;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\GlobalMapController;
use Illuminate\Support\Facades\Route;

Route::middleware(LogApiRequest::class)->group(function () {
    Route::get('/global-map', GlobalMapController::class)
        ->name('api.global-map');
    Route::get(
    '/economy',
    [EconomicController::class, 'index']
)->name('api.economy.index');

    Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

Route::get('/ports', [PortController::class, 'index']);

Route::get(
    '/news',
    [NewsController::class, 'index']
)->name('api.news.index');

Route::get(
    '/currency',
    [CurrencyController::class, 'index']
)->name('api.currency.index');

Route::get('/risk', [RiskController::class, 'show'])
    ->name('api.risk');

});
