<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Système de configuration centralisé (doc 02 — §6.bis).
 *
 * Une seule table clé/valeur typée pilote tous les prix, taux, durées et
 * feature-flags du marketplace, avec un journal d'audit immuable pour tracer
 * qui a changé quoi et quand.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');                          // payments, packs, auctions, finance, ads, estimation, partners, global…
            $table->string('key')->unique();                  // clé dottée complète : auctions.seller_commission_rate
            $table->text('value')->nullable();
            $table->enum('type', ['bool', 'int', 'decimal', 'string', 'json', 'percent'])->default('string');
            $table->string('label');                          // libellé affiché en admin
            $table->text('description')->nullable();          // aide contextuelle
            $table->boolean('is_toggle')->default(false);     // switch on/off
            $table->boolean('is_secret')->default(false);     // masqué (clés passerelles) → input password
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index('group');
        });

        Schema::create('setting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('setting_key');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();

            $table->index('setting_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_logs');
        Schema::dropIfExists('settings');
    }
};
