<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('display_number', 32);      // ex. +212 657 14 00 50
            $table->string('phone_id', 64)->unique();  // Meta phone_number_id
            $table->string('waba_id', 64)->nullable();
            $table->string('label')->nullable();       // libellé interne (ex. "SAV Casablanca")
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('verified_name')->nullable();     // nom affiché WhatsApp
            $table->string('quality_rating', 32)->nullable();// GREEN / YELLOW / RED
            $table->timestamps();

            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_numbers');
    }
};
