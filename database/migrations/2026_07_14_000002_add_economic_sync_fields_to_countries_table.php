<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->unsignedSmallInteger('gdp_data_year')
                ->nullable()
                ->after('gdp_usd_billion');

            $table->unsignedSmallInteger('inflation_data_year')
                ->nullable()
                ->after('inflation_rate');

            $table->unsignedSmallInteger('population_data_year')
                ->nullable()
                ->after('population');

            $table->timestamp('economic_synced_at')
                ->nullable()
                ->after('weather_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'gdp_data_year',
                'inflation_data_year',
                'population_data_year',
                'economic_synced_at',
            ]);
        });
    }
};