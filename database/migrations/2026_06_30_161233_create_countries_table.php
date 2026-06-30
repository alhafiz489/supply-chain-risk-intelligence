<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('iso2', 5)->unique();
            $table->string('region')->nullable();
            $table->string('capital')->nullable();
            $table->string('currency_code', 10)->nullable();

            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();

            $table->decimal('gdp_usd_billion', 15, 2)->default(0);
            $table->decimal('inflation_rate', 8, 2)->default(0);
            $table->decimal('population_million', 12, 2)->default(0);

            $table->decimal('exchange_rate_to_idr', 15, 2)->default(0);
            $table->decimal('currency_volatility_percent', 8, 2)->default(0);

            $table->string('weather_condition')->default('Clear');
            $table->decimal('temperature', 8, 2)->default(0);
            $table->decimal('rainfall_mm', 8, 2)->default(0);
            $table->decimal('wind_speed_kmh', 8, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};