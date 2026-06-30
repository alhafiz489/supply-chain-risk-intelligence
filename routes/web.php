<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/language/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'id'])) {
        abort(400);
    }

    Session::put('locale', $locale);

    return redirect()->back();
})->name('language.switch');