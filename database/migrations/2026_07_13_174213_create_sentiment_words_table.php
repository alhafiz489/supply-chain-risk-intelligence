<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sentiment_words', function (Blueprint $table) {
            $table->id();
            $table->string('word', 100);
            $table->enum('type', ['positive', 'negative']);
            $table->unsignedTinyInteger('weight')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['word', 'type'],
                'sentiment_words_word_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sentiment_words');
    }
};