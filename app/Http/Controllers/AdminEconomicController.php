<?php

namespace App\Http\Controllers;

use App\Services\WorldBankEconomicService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AdminEconomicController extends Controller
{
    public function sync(
        WorldBankEconomicService $economicService
    ): RedirectResponse {
        try {
            set_time_limit(0);

            $result = $economicService->syncAll();

            $message = app()->getLocale() === 'id'
                ? "Data ekonomi {$result['updated']} negara/wilayah berhasil diperbarui dari World Bank."
                : "Economic data for {$result['updated']} countries/territories was updated from the World Bank.";

            if ($result['without_data'] > 0) {
                $message .= app()->getLocale() === 'id'
                    ? " {$result['without_data']} negara/wilayah belum memiliki data."
                    : " {$result['without_data']} countries/territories do not yet have data.";
            }

            if ($result['failed'] > 0) {
                $message .= app()->getLocale() === 'id'
                    ? " {$result['failed']} negara/wilayah gagal diproses."
                    : " {$result['failed']} countries/territories failed.";
            }

            return back()->with('success', $message);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Sinkronisasi data ekonomi gagal. Periksa koneksi internet dan storage/logs/laravel.log.'
                    : 'Economic synchronization failed. Check the internet connection and storage/logs/laravel.log.'
            );
        }
    }
}