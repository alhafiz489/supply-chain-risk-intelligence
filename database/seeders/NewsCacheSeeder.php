<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Database\Seeder;

class NewsCacheSeeder extends Seeder
{
    public function run(): void
    {
        $news = [
            [
                'country' => 'Indonesia',
                'title' => 'Port activity remains stable despite seasonal rain',
                'category' => 'Logistics',
                'sentiment' => 'Neutral',
            ],
            [
                'country' => 'China',
                'title' => 'Shipping delays increase due to port congestion',
                'category' => 'Shipping',
                'sentiment' => 'Negative',
            ],
            [
                'country' => 'Germany',
                'title' => 'Manufacturing exports show stable recovery',
                'category' => 'Economy',
                'sentiment' => 'Positive',
            ],
            [
                'country' => 'Australia',
                'title' => 'Weather disruption affects several shipping schedules',
                'category' => 'Weather',
                'sentiment' => 'Negative',
            ],
            [
                'country' => 'Japan',
                'title' => 'Storm warning may affect coastal logistics operations',
                'category' => 'Weather',
                'sentiment' => 'Negative',
            ],
            [
                'country' => 'Singapore',
                'title' => 'Trade hub performance remains strong and efficient',
                'category' => 'Trade',
                'sentiment' => 'Positive',
            ],
        ];

        foreach ($news as $item) {
            $country = Country::where('name', $item['country'])->first();

            if ($country) {
                NewsCache::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'title' => $item['title'],
                    ],
                    [
                        'category' => $item['category'],
                        'sentiment' => $item['sentiment'],
                        'summary' => 'Initial dummy news data for project development.',
                        'published_date' => now()->toDateString(),
                    ]
                );
            }
        }
    }
}