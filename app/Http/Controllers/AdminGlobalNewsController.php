<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class AdminGlobalNewsController extends Controller
{
    public function sync(): RedirectResponse
    {
        Artisan::call(
            'supplyguard:sync-global-news'
        );

        $exitCode = Artisan::output();

        return back()->with(
            'success',
            app()->getLocale() === 'id'
                ? 'Sinkronisasi batch berita global selesai. Detail proses tersedia di terminal atau log Laravel.'
                : 'Global news batch synchronization completed. Process details are available in the terminal or Laravel log.'
        );
    }
}