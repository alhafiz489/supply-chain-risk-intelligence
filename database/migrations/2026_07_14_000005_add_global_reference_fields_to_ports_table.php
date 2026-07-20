<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->string('unlocode', 5)
                ->nullable()
                ->unique()
                ->after('country_id');

            $table->string('location_code', 3)
                ->nullable()
                ->after('unlocode');

            $table->string('name_without_diacritics')
                ->nullable()
                ->after('name');

            $table->string('subdivision_code', 10)
                ->nullable()
                ->after('city');

            $table->string('change_indicator', 2)
                ->nullable()
                ->after('subdivision_code');

            $table->string('status_code', 2)
                ->nullable()
                ->after('change_indicator');

            $table->string('function_code', 8)
                ->nullable()
                ->after('status_code');

            $table->string('iata_code', 3)
                ->nullable()
                ->after('function_code');

            $table->string('source', 80)
                ->default('Manual')
                ->after('delay_days');

            $table->string('source_version', 30)
                ->nullable()
                ->after('source');

            $table->text('source_url')
                ->nullable()
                ->after('source_version');

            $table->string('data_status', 30)
                ->default('manual')
                ->after('source_url');

            $table->boolean('is_reference_active')
                ->default(true)
                ->after('data_status');

            $table->text('remarks')
                ->nullable()
                ->after('is_reference_active');

            $table->timestamp('synced_at')
                ->nullable()
                ->after('remarks');

            $table->index(
                ['country_id', 'source', 'is_reference_active'],
                'ports_country_source_active_index'
            );
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->decimal('latitude', 10, 6)
                ->nullable()
                ->change();

            $table->decimal('longitude', 10, 6)
                ->nullable()
                ->change();

            $table->string('congestion_level')
                ->nullable()
                ->default(null)
                ->change();

            $table->integer('delay_days')
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropIndex(
                'ports_country_source_active_index'
            );

            $table->dropUnique(
                'ports_unlocode_unique'
            );

            $table->dropColumn([
                'unlocode',
                'location_code',
                'name_without_diacritics',
                'subdivision_code',
                'change_indicator',
                'status_code',
                'function_code',
                'iata_code',
                'source',
                'source_version',
                'source_url',
                'data_status',
                'is_reference_active',
                'remarks',
                'synced_at',
            ]);
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->decimal('latitude', 10, 6)
                ->nullable(false)
                ->change();

            $table->decimal('longitude', 10, 6)
                ->nullable(false)
                ->change();

            $table->string('congestion_level')
                ->nullable(false)
                ->default('Low')
                ->change();

            $table->integer('delay_days')
                ->nullable(false)
                ->default(0)
                ->change();
        });
    }
};