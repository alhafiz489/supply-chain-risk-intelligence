<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan informasi bahasa utama setiap negara.
     */
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table
                ->string('primary_language_code', 10)
                ->nullable()
                ->after('currency_code')
                ->index();

            $table
                ->string('primary_language_name', 100)
                ->nullable()
                ->after('primary_language_code');

            $table
                ->string('primary_language_native_name', 100)
                ->nullable()
                ->after('primary_language_name');

            $table
                ->string('text_direction', 3)
                ->default('ltr')
                ->after('primary_language_native_name');
        });
    }

    /**
     * Menghapus kembali kolom bahasa apabila migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex([
                'primary_language_code',
            ]);

            $table->dropColumn([
                'primary_language_code',
                'primary_language_name',
                'primary_language_native_name',
                'text_direction',
            ]);
        });
    }
};