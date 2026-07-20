<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->date('currency_rate_date')
                ->nullable()
                ->after('currency_volatility_percent');

            $table->string('currency_source', 50)
                ->nullable()
                ->after('currency_rate_date');

            $table->string('currency_data_status', 30)
                ->default('unavailable')
                ->after('currency_source');

            $table->timestamp('currency_synced_at')
                ->nullable()
                ->after('economic_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn([
                'currency_rate_date',
                'currency_source',
                'currency_data_status',
                'currency_synced_at',
            ]);
        });
    }
};