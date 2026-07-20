<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Locale Laravel yang sudah memiliki file terjemahan
        |--------------------------------------------------------------------------
        |
        | Bahasa lain tetap disimpan sebagai translation_target_locale, tetapi
        | sementara menggunakan English agar key terjemahan tidak tampil mentah.
        |
        */

        $supportedLocales = [
            'id',
            'en',
        ];

        $locale = strtolower(
            (string) $request->session()->get(
                'locale',
                config('app.locale', 'id')
            )
        );

        if (! in_array(
            $locale,
            $supportedLocales,
            true
        )) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}