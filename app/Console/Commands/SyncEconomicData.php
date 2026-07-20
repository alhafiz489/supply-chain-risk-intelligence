<?php

namespace App\Console\Commands;

use App\Services\WorldBankEconomicService;
use Illuminate\Console\Command;
use Throwable;

class SyncEconomicData extends Command
{
    protected $signature =
        'supplyguard:sync-economy';

    protected $description =
        'Synchronize GDP, inflation, and population from World Bank';

    public function handle(
        WorldBankEconomicService $economicService
    ): int {
        try {
            $this->info(
                'Sinkronisasi data ekonomi World Bank dimulai.'
            );

            $result = $economicService->syncAll();

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    [
                        'Negara/wilayah diperbarui',
                        $result['updated'],
                    ],
                    [
                        'GDP berhasil diperbarui',
                        $result['gdp_updated'],
                    ],
                    [
                        'Inflasi berhasil diperbarui',
                        $result['inflation_updated'],
                    ],
                    [
                        'Populasi berhasil diperbarui',
                        $result['population_updated'],
                    ],
                    [
                        'Tidak memiliki data World Bank',
                        $result['without_data'],
                    ],
                    [
                        'Gagal diproses',
                        $result['failed'],
                    ],
                ]
            );

            $this->line(
                'Periode pencarian data: '
                .$result['period']
            );

            if ($result['failed_countries'] !== []) {
                $this->warn(
                    'Negara gagal: '
                    .implode(
                        ', ',
                        $result['failed_countries']
                    )
                );
            }

            $this->info(
                'Sinkronisasi ekonomi selesai. Risk Score global belum dihitung ulang sampai data kurs, berita, dan pelabuhan tersedia.'
            );

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}