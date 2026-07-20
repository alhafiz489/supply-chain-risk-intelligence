<?php

namespace App\Http\Controllers;

use App\Services\UnlocodePortService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AdminGlobalPortController extends Controller
{
    public function sync(
        UnlocodePortService $portService
    ): RedirectResponse {
        try {
            set_time_limit(0);

            $result = $portService->sync();

            $message = app()->getLocale() === 'id'
                ? "Referensi {$result['sea_port_rows']} pelabuhan berhasil diproses. {$result['created']} data baru dan {$result['updated']} data diperbarui."
                : "{$result['sea_port_rows']} port references were processed. {$result['created']} new records and {$result['updated']} records were updated.";

            if (
                $result['without_coordinates'] > 0
            ) {
                $message .= app()->getLocale() === 'id'
                    ? " {$result['without_coordinates']} pelabuhan belum memiliki koordinat."
                    : " {$result['without_coordinates']} ports do not have coordinates.";
            }

            return back()->with(
                'success',
                $message
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                app()->getLocale() === 'id'
                    ? 'Sinkronisasi pelabuhan global gagal. Periksa koneksi internet dan storage/logs/laravel.log.'
                    : 'Global port synchronization failed. Check the internet connection and storage/logs/laravel.log.'
            );
        }
    }
}