<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('endpoint');
            $table->string('route_name')->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('request_payload')->nullable();
            $table->longText('response_payload')->nullable();
            $table->timestamps();

            $table->index('method');
            $table->index('status_code');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};