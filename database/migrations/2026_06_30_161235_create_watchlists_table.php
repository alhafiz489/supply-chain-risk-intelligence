<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('notes')->nullable();
            $table->integer('last_risk_score')->nullable();
            $table->string('last_risk_label')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};