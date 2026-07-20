<?php

namespace App\Console\Commands;

use App\Services\AutomaticTranslationService;
use Illuminate\Console\Command;

class TestAutomaticTranslation extends Command
{
    protected $signature =
        'supplyguard:test-translation
         {target : Kode bahasa tujuan, contoh ja atau ar}
         {text=Supply chain risk monitoring : Teks yang diterjemahkan}
         {--source=en : Kode bahasa sumber}';

    protected $description =
        'Menguji provider dan cache terjemahan otomatis SupplyGuard';

    public function handle(
        AutomaticTranslationService $translation
    ): int {
        $source = (string) $this->option('source');
        $target = (string) $this->argument('target');
        $text = (string) $this->argument('text');

        $result = $translation->translateMany(
            [$text],
            $target,
            $source
        )[$text] ?? $text;

        $this->table(
            ['Sumber', 'Tujuan', 'Teks Asli', 'Hasil'],
            [[
                $source,
                $target,
                $text,
                $result,
            ]]
        );

        if ($result === $text && $source !== $target) {
            $this->warn(
                'Hasil masih sama dengan teks sumber. '
                .'Periksa konfigurasi provider dan storage/logs/laravel.log.'
            );
        } else {
            $this->info(
                'Terjemahan berhasil dan disimpan ke translation_cache.'
            );
        }

        return self::SUCCESS;
    }
}
