<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class TranslationStatus extends Command
{
    protected $signature = 'supplyguard:translation-status';

    protected $description =
        'Memeriksa koneksi LibreTranslate dan model bahasa utama SupplyGuard';

    public function handle(): int
    {
        $enabled = (bool) config('services.translation.enabled');
        $provider = strtolower((string) config('services.translation.provider'));
        $url = rtrim(
            (string) config('services.translation.libretranslate.url'),
            '/'
        );

        $this->table(['Konfigurasi', 'Nilai'], [
            ['Aktif', $enabled ? 'Ya' : 'Tidak'],
            ['Provider', $provider],
            ['Endpoint', $url],
        ]);

        if (! $enabled || $provider !== 'libretranslate') {
            $this->error('LibreTranslate belum menjadi provider aktif.');

            return self::FAILURE;
        }

        try {
            $response = Http::connectTimeout(3)->timeout(10)->get($url.'/languages');

            if (! $response->successful()) {
                $this->error("Provider merespons HTTP {$response->status()}.");

                return self::FAILURE;
            }

            $languages = collect($response->json())->pluck('name', 'code');
            $required = [
                'en' => 'English',
                'id' => 'Bahasa Indonesia',
                'ja' => '日本語',
                'ar' => 'العربية',
                'zh-Hans' => '中文',
            ];
            $rows = collect($required)->map(
                fn (string $name, string $code): array => [
                    $code,
                    $name,
                    $languages->has($code) ? 'Tersedia' : 'Belum tersedia',
                ]
            )->values()->all();

            $this->table(['Kode', 'Bahasa', 'Status model'], $rows);
            $missing = array_keys(array_filter(
                $required,
                fn (string $name, string $code): bool => ! $languages->has($code),
                ARRAY_FILTER_USE_BOTH
            ));

            if ($missing !== []) {
                $this->error('Model belum lengkap: '.implode(', ', $missing));

                return self::FAILURE;
            }

            $this->info('Provider online dan seluruh model utama tersedia.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Provider tidak dapat dihubungi: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
