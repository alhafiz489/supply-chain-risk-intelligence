<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            [
                'country' => 'Indonesia',
                'name' => 'Port of Tanjung Priok',
                'city' => 'Jakarta',
                'latitude' => -6.1045,
                'longitude' => 106.8800,
                'congestion_level' => 'Medium',
                'delay_days' => 2,
            ],
            [
                'country' => 'China',
                'name' => 'Port of Shanghai',
                'city' => 'Shanghai',
                'latitude' => 31.2304,
                'longitude' => 121.4737,
                'congestion_level' => 'High',
                'delay_days' => 5,
            ],
            [
                'country' => 'Germany',
                'name' => 'Port of Hamburg',
                'city' => 'Hamburg',
                'latitude' => 53.5511,
                'longitude' => 9.9937,
                'congestion_level' => 'Low',
                'delay_days' => 1,
            ],
            [
                'country' => 'Australia',
                'name' => 'Port Botany',
                'city' => 'Sydney',
                'latitude' => -33.9667,
                'longitude' => 151.2167,
                'congestion_level' => 'Medium',
                'delay_days' => 3,
            ],
            [
                'country' => 'Japan',
                'name' => 'Port of Yokohama',
                'city' => 'Yokohama',
                'latitude' => 35.4437,
                'longitude' => 139.6380,
                'congestion_level' => 'Medium',
                'delay_days' => 2,
            ],
            [
                'country' => 'Singapore',
                'name' => 'Port of Singapore',
                'city' => 'Singapore',
                'latitude' => 1.2644,
                'longitude' => 103.8400,
                'congestion_level' => 'Low',
                'delay_days' => 1,
            ],
        ];

        foreach ($ports as $item) {
            $country = Country::where('name', $item['country'])->first();

            if ($country) {
                Port::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $item['name'],
                    ],
                    [
                        'city' => $item['city'],
                        'latitude' => $item['latitude'],
                        'longitude' => $item['longitude'],
                        'congestion_level' => $item['congestion_level'],
                        'delay_days' => $item['delay_days'],
                    ]
                );
            }
        }
    }
}