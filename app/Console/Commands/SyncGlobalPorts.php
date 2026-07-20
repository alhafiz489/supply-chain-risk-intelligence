<?php

namespace App\Console\Commands;

use App\Services\UnlocodePortService;
use Illuminate\Console\Command;
use Throwable;

class SyncGlobalPorts extends Command
{
    protected $signature =
        'supplyguard:sync-global-ports
         {--country= : ISO alpha-2 country code, for example ID}
         {--limit= : Maximum sea-port records to process}
         {--force-download : Download the dataset again}';

    protected $description =
        'Synchronize global port reference data from UN/LOCODE';

    public function handle(
        UnlocodePortService $portService
    ): int {
        try {
            $country = $this->option(
                'country'
            );

            $limit =
                $this->option('limit') !== null
                    ? max(
                        1,
                        (int) $this->option(
                            'limit'
                        )
                    )
                    : null;

            $this->info(
                'Sinkronisasi referensi pelabuhan global dimulai.'
            );

            $result = $portService->sync(
                $country !== null
                    ? (string) $country
                    : null,
                $limit,
                (bool) $this->option(
                    'force-download'
                )
            );

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    [
                        'Baris dataset dibaca',
                        $result['rows_read'],
                    ],
                    [
                        'Pelabuhan diproses',
                        $result[
                            'sea_port_rows'
                        ],
                    ],
                    [
                        'Pelabuhan baru',
                        $result['created'],
                    ],
                    [
                        'Pelabuhan diperbarui',
                        $result['updated'],
                    ],
                    [
                        'Tanpa koordinat',
                        $result[
                            'without_coordinates'
                        ],
                    ],
                    [
                        'Negara tidak ditemukan',
                        $result[
                            'countries_missing'
                        ],
                    ],
                    [
                        'Baris dilewati',
                        $result['skipped'],
                    ],
                    [
                        'Record lama dinonaktifkan',
                        $result[
                            'marked_inactive'
                        ],
                    ],
                ]
            );

            $this->line(
                'Versi sumber: '
                .$result['source_version']
            );

            $this->info(
                'Sinkronisasi pelabuhan global selesai. Data kongesti dan delay tidak diisi karena UN/LOCODE adalah data referensi lokasi, bukan data operasional realtime.'
            );

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }
}