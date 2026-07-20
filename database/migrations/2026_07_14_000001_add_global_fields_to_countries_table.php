<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('official_name')
                ->nullable()
                ->after('name');

            $table->string('iso3', 3)
                ->nullable()
                ->unique()
                ->after('iso2');

            $table->string('subregion')
                ->nullable()
                ->after('region');

            $table->unsignedBigInteger('population')
                ->default(0)
                ->after('population_million');

            $table->boolean('is_sovereign')
                ->default(false)
                ->after('population');

            $table->boolean('is_un_member')
                ->default(false)
                ->after('is_sovereign');

            $table->boolean('is_dependency')
                ->default(false)
                ->after('is_un_member');

            $table->text('flag_url')
                ->nullable()
                ->after('is_dependency');

            $table->unsignedTinyInteger('data_completeness_percent')
                ->default(0)
                ->after('flag_url');

            $table->string('risk_data_status', 30)
                ->default('incomplete')
                ->after('data_completeness_percent');

            $table->timestamp('master_synced_at')
                ->nullable()
                ->after('risk_data_status');

            $table->timestamp('weather_synced_at')
                ->nullable()
                ->after('master_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropUnique('countries_iso3_unique');

            $table->dropColumn([
                'official_name',
                'iso3',
                'subregion',
                'population',
                'is_sovereign',
                'is_un_member',
                'is_dependency',
                'flag_url',
                'data_completeness_percent',
                'risk_data_status',
                'master_synced_at',
                'weather_synced_at',
            ]);
        });
    }
};