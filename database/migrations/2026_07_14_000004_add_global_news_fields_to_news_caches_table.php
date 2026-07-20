<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_caches', function (Blueprint $table) {
            $table->string('external_id', 64)
                ->nullable()
                ->unique()
                ->after('country_id');

            $table->text('url')
                ->nullable()
                ->after('summary');

            $table->text('image_url')
                ->nullable()
                ->after('url');

            $table->string('source_name')
                ->nullable()
                ->after('image_url');

            $table->text('source_url')
                ->nullable()
                ->after('source_name');

            $table->string('language', 10)
                ->default('en')
                ->after('source_url');

            $table->unsignedInteger('positive_score')
                ->default(0)
                ->after('sentiment');

            $table->unsignedInteger('negative_score')
                ->default(0)
                ->after('positive_score');

            $table->json('matched_words')
                ->nullable()
                ->after('negative_score');

            $table->dateTime('published_at')
                ->nullable()
                ->index()
                ->after('published_date');

            $table->timestamp('synced_at')
                ->nullable()
                ->after('published_at');

            $table->index([
                'country_id',
                'sentiment',
                'published_at',
            ], 'news_country_sentiment_published_index');
        });
    }

    public function down(): void
    {
        Schema::table('news_caches', function (Blueprint $table) {
            $table->dropIndex(
                'news_country_sentiment_published_index'
            );

            $table->dropUnique(
                'news_caches_external_id_unique'
            );

            $table->dropColumn([
                'external_id',
                'url',
                'image_url',
                'source_name',
                'source_url',
                'language',
                'positive_score',
                'negative_score',
                'matched_words',
                'published_at',
                'synced_at',
            ]);
        });
    }
};