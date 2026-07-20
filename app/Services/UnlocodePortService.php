<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SplFileObject;
use Throwable;

class UnlocodePortService
{
    private const SOURCE_NAME =
        'UNECE UN/LOCODE';

    private const OFFICIAL_SOURCE_URL =
        'https://unece.org/trade/cefact/UNLOCODE-Download';

    private const BATCH_SIZE = 500;

    public function sync(
        ?string $countryIso2 = null,
        ?int $limit = null,
        bool $forceDownload = false
    ): array {
        $countryIso2 = $countryIso2 !== null
            ? strtoupper(trim($countryIso2))
            : null;

        $limit = $limit !== null
            ? max(1, $limit)
            : null;

        $startedAt = now();
        $filePath = $this->datasetPath();

        $this->downloadDataset(
            $filePath,
            $forceDownload
        );

        $countryIds = Country::query()
            ->whereNotNull('iso2')
            ->pluck('id', 'iso2')
            ->mapWithKeys(
                fn ($id, $iso2) => [
                    strtoupper((string) $iso2) =>
                        (int) $id,
                ]
            )
            ->all();

        $existingLocodes = Port::query()
            ->whereNotNull('unlocode')
            ->pluck('unlocode')
            ->mapWithKeys(
                fn ($unlocode) => [
                    (string) $unlocode => true,
                ]
            )
            ->all();

        $file = new SplFileObject(
            $filePath,
            'r'
        );

        $file->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::DROP_NEW_LINE
        );

        $header = null;
        $batch = [];

        $rowsRead = 0;
        $seaPortRows = 0;
        $created = 0;
        $updated = 0;
        $withoutCoordinates = 0;
        $countriesMissing = 0;
        $skipped = 0;

        foreach ($file as $row) {
            if (
                ! is_array($row)
                || $row === [null]
            ) {
                continue;
            }

            if ($header === null) {
                $header = array_map(
                    fn ($value) => trim(
                        (string) $value
                    ),
                    $row
                );

                if (isset($header[0])) {
                    $header[0] = ltrim(
                        $header[0],
                        "\xEF\xBB\xBF"
                    );
                }

                continue;
            }

            $rowsRead++;

            if (count($row) !== count($header)) {
                $skipped++;
                continue;
            }

            $record = array_combine(
                $header,
                $row
            );

            if (! is_array($record)) {
                $skipped++;
                continue;
            }

            $functionCode = trim(
                (string) (
                    $record['Function']
                    ?? ''
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Function classifier position 1 = port
            |--------------------------------------------------------------------------
            |
            | Hanya lokasi dengan karakter pertama "1" yang dimasukkan sebagai
            | pelabuhan. Data terminal jalan, bandara, dan lokasi lain dilewati.
            |
            */

            if (
                $functionCode === ''
                || substr($functionCode, 0, 1)
                    !== '1'
            ) {
                continue;
            }

            $iso2 = strtoupper(
                trim(
                    (string) (
                        $record['Country']
                        ?? ''
                    )
                )
            );

            if (
                $countryIso2 !== null
                && $iso2 !== $countryIso2
            ) {
                continue;
            }

            $locationCode = strtoupper(
                trim(
                    (string) (
                        $record['Location']
                        ?? ''
                    )
                )
            );

            if (
                strlen($iso2) !== 2
                || strlen($locationCode) !== 3
            ) {
                $skipped++;
                continue;
            }

            $countryId =
                $countryIds[$iso2]
                ?? null;

            if ($countryId === null) {
                $countriesMissing++;
                continue;
            }

            $name = trim(
                (string) (
                    $record['Name']
                    ?? ''
                )
            );

            if ($name === '') {
                $skipped++;
                continue;
            }

            $unlocode =
                $iso2.$locationCode;

            [$latitude, $longitude] =
                $this->parseCoordinates(
                    trim(
                        (string) (
                            $record[
                                'Coordinates'
                            ]
                            ?? ''
                        )
                    )
                );

            if (
                $latitude === null
                || $longitude === null
            ) {
                $withoutCoordinates++;
            }

            $statusCode = strtoupper(
                trim(
                    (string) (
                        $record['Status']
                        ?? ''
                    )
                )
            );

            $batch[] = [
                'country_id' => $countryId,
                'unlocode' => $unlocode,
                'location_code' =>
                    $locationCode,
                'name' => $name,
                'name_without_diacritics' =>
                    $this->nullable(
                        $record[
                            'NameWoDiacritics'
                        ]
                        ?? null
                    ),
                'city' => $name,
                'subdivision_code' =>
                    $this->nullable(
                        $record[
                            'Subdivision'
                        ]
                        ?? null
                    ),
                'change_indicator' =>
                    $this->nullable(
                        $record['Change']
                        ?? null
                    ),
                'status_code' =>
                    $statusCode !== ''
                        ? $statusCode
                        : null,
                'function_code' =>
                    $functionCode,
                'iata_code' =>
                    $this->nullable(
                        $record['IATA']
                        ?? null
                    ),
                'latitude' => $latitude,
                'longitude' => $longitude,

                /*
                 * UN/LOCODE tidak menyediakan data kongesti atau delay realtime.
                 * Nilai tersebut sengaja dikosongkan agar tidak menjadi data palsu.
                 */
                'congestion_level' => null,
                'delay_days' => null,

                'source' =>
                    self::SOURCE_NAME,
                'source_version' =>
                    $this->version(),
                'source_url' =>
                    self::OFFICIAL_SOURCE_URL,
                'data_status' =>
                    'reference_only',
                'is_reference_active' =>
                    $statusCode !== 'XX',
                'remarks' =>
                    $this->nullable(
                        $record['Remarks']
                        ?? null
                    ),
                'synced_at' => $startedAt,
                'created_at' => $startedAt,
                'updated_at' => $startedAt,
            ];

            $seaPortRows++;

            if (
                isset(
                    $existingLocodes[
                        $unlocode
                    ]
                )
            ) {
                $updated++;
            } else {
                $created++;
                $existingLocodes[
                    $unlocode
                ] = true;
            }

            if (
                count($batch)
                >= self::BATCH_SIZE
            ) {
                $this->upsertBatch($batch);
                $batch = [];
            }

            if (
                $limit !== null
                && $seaPortRows >= $limit
            ) {
                break;
            }
        }

        if ($batch !== []) {
            $this->upsertBatch($batch);
        }

        $markedInactive = 0;

        /*
        |--------------------------------------------------------------------------
        | Tandai record versi lama hanya pada sinkronisasi global penuh
        |--------------------------------------------------------------------------
        |
        | Sinkronisasi berdasarkan negara atau limit tidak boleh menonaktifkan
        | record negara lain.
        |
        */

        if (
            $countryIso2 === null
            && $limit === null
        ) {
            $markedInactive = Port::query()
                ->where(
                    'source',
                    self::SOURCE_NAME
                )
                ->where(function ($query) use (
                    $startedAt
                ) {
                    $query
                        ->whereNull('synced_at')
                        ->orWhere(
                            'synced_at',
                            '<',
                            $startedAt
                        );
                })
                ->update([
                    'is_reference_active' =>
                        false,
                    'updated_at' => now(),
                ]);
        }

        return [
            'rows_read' => $rowsRead,
            'sea_port_rows' => $seaPortRows,
            'created' => $created,
            'updated' => $updated,
            'without_coordinates' =>
                $withoutCoordinates,
            'countries_missing' =>
                $countriesMissing,
            'skipped' => $skipped,
            'marked_inactive' =>
                $markedInactive,
            'source_version' =>
                $this->version(),
            'dataset_path' => $filePath,
        ];
    }

    private function downloadDataset(
        string $filePath,
        bool $forceDownload
    ): void {
        $maxAgeHours = max(
            1,
            (int) config(
                'services.unlocode.cache_hours',
                168
            )
        );

        $isFresh =
            File::exists($filePath)
            && File::lastModified($filePath)
                >= now()
                    ->subHours(
                        $maxAgeHours
                    )
                    ->timestamp;

        if (
            ! $forceDownload
            && $isFresh
        ) {
            return;
        }

        File::ensureDirectoryExists(
            dirname($filePath)
        );

        $temporaryPath =
            $filePath.'.download';

        $response = Http::accept(
            'text/csv'
        )
            ->connectTimeout(20)
            ->timeout(180)
            ->retry(
                3,
                1500,
                null,
                false
            )
            ->sink($temporaryPath)
            ->get(
                $this->datasetUrl()
            );

        $this->ensureSuccessful(
            $response
        );

        if (
            ! File::exists($temporaryPath)
            || File::size($temporaryPath)
                < 1000
        ) {
            File::delete(
                $temporaryPath
            );

            throw new RuntimeException(
                'File UN/LOCODE yang diunduh tidak valid.'
            );
        }

        File::move(
            $temporaryPath,
            $filePath
        );
    }

    private function upsertBatch(
        array $batch
    ): void {
        DB::table('ports')->upsert(
            $batch,
            ['unlocode'],
            [
                'country_id',
                'location_code',
                'name',
                'name_without_diacritics',
                'city',
                'subdivision_code',
                'change_indicator',
                'status_code',
                'function_code',
                'iata_code',
                'latitude',
                'longitude',
                'congestion_level',
                'delay_days',
                'source',
                'source_version',
                'source_url',
                'data_status',
                'is_reference_active',
                'remarks',
                'synced_at',
                'updated_at',
            ]
        );
    }

    private function parseCoordinates(
        string $coordinates
    ): array {
        if ($coordinates === '') {
            return [null, null];
        }

        $matched = preg_match(
            '/^(\d{2})(\d{2})([NS])\s+(\d{3})(\d{2})([EW])$/',
            $coordinates,
            $parts
        );

        if ($matched !== 1) {
            return [null, null];
        }

        $latitude =
            (int) $parts[1]
            + ((int) $parts[2] / 60);

        $longitude =
            (int) $parts[4]
            + ((int) $parts[5] / 60);

        if ($parts[3] === 'S') {
            $latitude *= -1;
        }

        if ($parts[6] === 'W') {
            $longitude *= -1;
        }

        return [
            round($latitude, 6),
            round($longitude, 6),
        ];
    }

    private function nullable(
        mixed $value
    ): ?string {
        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    private function datasetPath(): string
    {
        return storage_path(
            'app/supplyguard/unlocode/code-list-'
            .$this->version()
            .'.csv'
        );
    }

    private function datasetUrl(): string
    {
        return (string) config(
            'services.unlocode.dataset_url',
            'https://datahub.io/core/un-locode/_r/-/data/code-list.csv'
        );
    }

    private function version(): string
    {
        return (string) config(
            'services.unlocode.version',
            '2025-1'
        );
    }

    private function ensureSuccessful(
        Response $response
    ): void {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException(
            'Unduhan dataset UN/LOCODE gagal. HTTP status: '
            .$response->status()
            .'.'
        );
    }
}