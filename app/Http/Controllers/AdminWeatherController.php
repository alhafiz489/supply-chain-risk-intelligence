<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\OpenMeteoWeatherService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AdminWeatherController extends Controller
{
    public function sync(
        OpenMeteoWeatherService $weatherService
    ): RedirectResponse {
        try {
            set_time_limit(0);

            $countries = Country::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('id')
                ->get();

            if ($countries->isEmpty()) {
                return back()->with(
                    'error',
                    app()->getLocale() === 'id'
                        ? 'Tidak ada negara yang memiliki koordinat.'
                        : 'No countries have usable coordinates.'
                );
            }

            $result = $weatherService->syncCountries($countries);

            $message = app()->getLocale() === 'id'
                ? "Cuaca {$result['processed']} negara/wilayah berhasil disinkronkan."
                : "Weather for {$result['processed']} countries/territories was synchronized.";

            if ($result['failed'] > 0) {
                $message .= app()->getLocale() === 'id'
                    ? " {$result['failed']} negara/wilayah gagal diproses."
                    : " {$result['failed']} countries/territories failed.";
            }

            if ($result['skipped'] > 0) {
                $message .= app()->getLocale() === 'id'
                    ? " {$result['skipped']} negara/wilayah dilewati karena tidak memiliki koordinat."
                    : " {$result['skipped']} countries/territories were skipped because coordinates were unavailable.";
            }

            return back()->with('success', $message);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Sinkronisasi cuaca gagal. Periksa koneksi internet dan storage/logs/laravel.log.'
                    : 'Weather synchronization failed. Check the internet connection and storage/logs/laravel.log.'
            );
        }
    }
}