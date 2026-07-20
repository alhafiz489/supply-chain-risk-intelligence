<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat cache hasil terjemahan otomatis untuk web pengguna.
     */
    public function up(): void
    {
        Schema::create('translation_cache', function (Blueprint $table) {
            $table->id();

            $table
                ->string('source_locale', 10)
                ->default('en');

            $table
                ->string('target_locale', 10);

            $table
                ->char('source_hash', 64);

            $table->longText('source_text');

            $table
                ->longText('translated_text')
                ->nullable();

            $table
                ->string('provider', 50)
                ->nullable();

            $table
                ->string('status', 20)
                ->default('pending');

            $table
                ->text('error_message')
                ->nullable();

            $table
                ->timestamp('translated_at')
                ->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'source_locale',
                    'target_locale',
                    'source_hash',
                ],
                'translation_cache_unique_text'
            );

            $table->index(
                [
                    'target_locale',
                    'status',
                ],
                'translation_cache_locale_status'
            );
        });
    }

    /**
     * Menghapus tabel cache terjemahan.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_cache');
    }
};