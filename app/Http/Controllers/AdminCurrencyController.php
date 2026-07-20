<?php

namespace App\Http\Controllers;

use App\Services\FrankfurterCurrencyService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AdminCurrencyController extends Controller
{
    public function sync(
        FrankfurterCurrencyService $currencyService
    ): RedirectResponse {
        try {
            set_time_limit(0);

            $result = $currencyService->syncAll();

            $message = app()->getLocale() === 'id'
                ? "Kurs {$result['updated_countries']} negara/wilayah berhasil diperbarui."
                : "Exchange rates for {$result['updated_countries']} countries/territories were updated.";

            if ($result['unsupported_countries'] > 0) {
                $message .= app()->getLocale() === 'id'
                    ? " {$result['unsupported_countries']} negara/wilayah belum memiliki kurs."
                    : " {$result['unsupported_countries']} countries/territories do not have an available rate.";
            }

            return back()->with('success', $message);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Sinkronisasi kurs gagal. Periksa koneksi internet dan storage/logs/laravel.log.'
                    : 'Currency synchronization failed. Check the internet connection and storage/logs/laravel.log.'
            );
        }
    }
}