<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Lepaskan foreign key country_id sementara
        |--------------------------------------------------------------------------
        */
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropForeign([
                'country_id',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Hapus unique lama pada country_id
        |--------------------------------------------------------------------------
        */
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropUnique(
                'watchlists_country_id_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | 3. Tambahkan user_id
        |--------------------------------------------------------------------------
        */
        Schema::table('watchlists', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | 4. Pasang kembali foreign key country_id
        |--------------------------------------------------------------------------
        */
        Schema::table('watchlists', function (Blueprint $table) {
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | 5. Buat kombinasi user dan negara menjadi unik
        |--------------------------------------------------------------------------
        */
        Schema::table('watchlists', function (Blueprint $table) {
            $table->unique(
                [
                    'user_id',
                    'country_id',
                ],
                'watchlists_user_country_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropUnique(
                'watchlists_user_country_unique'
            );
        });

        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'user_id'
            );
        });

        Schema::table('watchlists', function (Blueprint $table) {
            $table->unique(
                'country_id',
                'watchlists_country_id_unique'
            );
        });
    }
};