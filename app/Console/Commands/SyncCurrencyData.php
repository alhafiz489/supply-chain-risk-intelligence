<?php

namespace App\Console\Commands;

use App\Services\FrankfurterCurrencyService;
use Illuminate\Console\Command;
use Throwable;

class SyncCurrencyData extends Command
{
    protected $signature =
        'supplyguard:sync-currency';

    protected $description =
        'Synchronize exchange rates to IDR and currency volatility';

    public function handle(
        FrankfurterCurrencyService $currencyService
    ): int {
        try {
            $this->info(
                'Sinkronisasi kurs global Frankfurter dimulai.'
            );

            $result = $currencyService->syncAll();

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    [
                        'Negara/wilayah diperbarui',
                        $result['updated_countries'],
                    ],
                    [
                        'Kode mata uang diperbarui',
                        $result['updated_currencies'],
                    ],
                    [
                        'Negara tanpa kurs tersedia',
                        $result['unsupported_countries'],
                    ],
                    [
                        'Negara gagal diproses',
                        $result['failed_countries'],
                    ],
                ]
            );

            $this->line(
                'Periode volatilitas: '
                .$result['from']
                .' sampai '
                .$result['to']
            );

            if ($result['unsupported_currencies'] !== []) {
                $this->warn(
                    'Mata uang belum tersedia: '
                    .implode(
                        ', ',
                        $result['unsupported_currencies']
                    )
                );
            }

            if ($result['failed_names'] !== []) {
                $this->warn(
                    'Negara gagal: '
                    .implode(
                        ', ',
                        $result['failed_names']
                    )
                );
            }

            $this->info(
                'Sinkronisasi kurs selesai. Risk Score global belum dihitung ulang sampai berita dan pelabuhan global tersedia.'
            );

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}