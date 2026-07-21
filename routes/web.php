<?php

use App\Http\Controllers\UserLanguageController;
use App\Http\Controllers\AdminGlobalPortController;
use App\Http\Controllers\AdminGlobalNewsController;
use App\Http\Controllers\AdminCurrencyController;
use App\Http\Controllers\AdminWeatherController;
use App\Http\Controllers\AdminEconomicController;
use App\Http\Controllers\AdminApiLogController;
use App\Http\Controllers\AdminRiskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\SystemOverviewController;
use App\Http\Controllers\NewsPageController;
use App\Http\Controllers\DataCatalogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard.page');

    Route::get('/country-comparison', function () {
        return view('country-comparison');
    })->name('country.comparison');

    Route::get('/system-overview', SystemOverviewController::class)
        ->name('system.overview');

    Route::get('/news', NewsPageController::class)
        ->name('news.index');

    Route::get('/data/countries', [DataCatalogController::class, 'countries'])->name('data.countries');
    Route::get('/data/ports', [DataCatalogController::class, 'ports'])->name('data.ports');
    Route::get('/data/sentiments', [DataCatalogController::class, 'sentiments'])->name('data.sentiments');
    Route::get('/data/countries/{country}', [DataCatalogController::class, 'country'])->name('data.countries.show');
    Route::get('/data/ports/{port}', [DataCatalogController::class, 'port'])->name('data.ports.show');
    Route::get('/data/sentiments/{sentimentWord}', [DataCatalogController::class, 'sentiment'])->name('data.sentiments.show');
    Route::get('/news/{newsCache}', [DataCatalogController::class, 'news'])->name('news.show');

    Route::get('/watchlist', function () {
        return view('watchlist');
    })->name('watchlist.index');

    Route::get(
        '/watchlist/data',
        [WatchlistController::class, 'index']
    )->name('watchlist.data');

    Route::post(
        '/watchlist',
        [WatchlistController::class, 'store']
    )->name('watchlist.store');

    Route::delete(
        '/watchlist/{countryId}',
        [WatchlistController::class, 'destroyByCountry']
    )->name('watchlist.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('auth.register');

    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])
        ->name('admin.login');

    Route::post('/admin/login', [AuthController::class, 'adminLogin'])
        ->name('admin.authenticate');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [AdminController::class, 'dashboard']
        )->name('dashboard');

        Route::get(
            '/users',
            [AdminController::class, 'users']
        )->name('users.index');

        Route::patch(
            '/users/{user}/status',
            [AdminController::class, 'updateUserStatus']
        )->name('users.status');

        Route::get(
    '/ports',
    [AdminController::class, 'ports']
)->name('ports.index');

Route::get(
    '/ports/create',
    [AdminController::class, 'createPort']
)->name('ports.create');

Route::post(
    '/ports',
    [AdminController::class, 'storePort']
)->name('ports.store');

Route::get(
    '/ports/{port}/edit',
    [AdminController::class, 'editPort']
)->name('ports.edit');

Route::put(
    '/ports/{port}',
    [AdminController::class, 'updatePort']
)->name('ports.update');

Route::delete(
    '/ports/{port}',
    [AdminController::class, 'destroyPort']
)->name('ports.destroy');

        Route::get(
    '/news',
    [AdminController::class, 'news']
)->name('news.index');

Route::get(
    '/news/create',
    [AdminController::class, 'createNews']
)->name('news.create');

Route::post(
    '/news',
    [AdminController::class, 'storeNews']
)->name('news.store');

Route::get(
    '/news/{news}/edit',
    [AdminController::class, 'editNews']
)->name('news.edit');

Route::put(
    '/news/{news}',
    [AdminController::class, 'updateNews']
)->name('news.update');

Route::delete(
    '/news/{news}',
    [AdminController::class, 'destroyNews']
)->name('news.destroy');

Route::get(
    '/sentiment-words',
    [AdminController::class, 'sentimentWords']
)->name('sentiment-words.index');

Route::get(
    '/sentiment-words/create',
    [AdminController::class, 'createSentimentWord']
)->name('sentiment-words.create');

Route::post(
    '/sentiment-words',
    [AdminController::class, 'storeSentimentWord']
)->name('sentiment-words.store');

Route::get(
    '/sentiment-words/{sentimentWord}/edit',
    [AdminController::class, 'editSentimentWord']
)->name('sentiment-words.edit');

Route::put(
    '/sentiment-words/{sentimentWord}',
    [AdminController::class, 'updateSentimentWord']
)->name('sentiment-words.update');

Route::delete(
    '/sentiment-words/{sentimentWord}',
    [AdminController::class, 'destroySentimentWord']
)->name('sentiment-words.destroy');

Route::post(
    '/risks/recalculate',
    [AdminController::class, 'recalculateRisks']
)->name('risks.recalculate');

Route::get(
    '/risks',
    [AdminRiskController::class, 'index']
)->name('risks.index');

Route::post(
    '/risks/{country}/recalculate',
    [AdminRiskController::class, 'recalculateCountry']
)->name('risks.recalculate-country');

Route::get(
    '/api-logs',
    [AdminApiLogController::class, 'index']
)->name('api-logs.index');

Route::get(
    '/api-logs/{apiLog}',
    [AdminApiLogController::class, 'show']
)->name('api-logs.show');

Route::post(
    '/economy/sync',
    [AdminEconomicController::class, 'sync']
)->name('economy.sync');

Route::post(
    '/weather/sync',
    [AdminWeatherController::class, 'sync']
)->name('weather.sync');

Route::post(
    '/currency/sync',
    [AdminCurrencyController::class, 'sync']
)->name('currency.sync');

Route::post(
    '/global-news/sync',
    [AdminGlobalNewsController::class, 'sync']
)->name('global-news.sync');

Route::post(
    '/global-ports/sync',
    [AdminGlobalPortController::class, 'sync']
)->name('global-ports.sync');

    });

Route::get('/language/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'id'], true)) {
        abort(404);
    }

    session([
        'locale' => $locale,
    ]);

    app()->setLocale($locale);

    return redirect()->back();
})->name('language.switch');

Route::get(
    '/user/languages',
    [UserLanguageController::class, 'index']
)->name('user.languages.index');

Route::get(
    '/user/language/{country}',
    [UserLanguageController::class, 'switch']
)->name('user.language.switch');

Route::post(
    '/user/translate',
    [UserLanguageController::class, 'translate']
)->middleware('throttle:30,1')->name('user.translate');

Route::get(
    '/user/translation-status',
    [UserLanguageController::class, 'status']
)->middleware('throttle:20,1')->name('user.translation.status');
