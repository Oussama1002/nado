<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Demande différée (service NARSA en panne) : le vendeur peut désormais choisir
 * de recevoir le résultat par email ET/OU sur WhatsApp. On stocke le canal
 * choisi + le numéro WhatsApp (jamais le nom/adresse du propriétaire).
 *
 * Idempotent : run individuellement en prod via
 *   php artisan migrate --path=database/migrations/2026_11_12_000001_add_contact_channel_to_legal_checks.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('legal_checks')) {
            return;
        }

        Schema::table('legal_checks', function (Blueprint $table) {
            if (! Schema::hasColumn('legal_checks', 'contact_channel')) {
                $table->enum('contact_channel', ['email', 'whatsapp', 'both'])
                    ->default('email')
                    ->after('declared_status');
            }
            if (! Schema::hasColumn('legal_checks', 'contact_whatsapp')) {
                $table->string('contact_whatsapp')->nullable()->after('contact_channel');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('legal_checks')) {
            return;
        }

        Schema::table('legal_checks', function (Blueprint $table) {
            if (Schema::hasColumn('legal_checks', 'contact_whatsapp')) {
                $table->dropColumn('contact_whatsapp');
            }
            if (Schema::hasColumn('legal_checks', 'contact_channel')) {
                $table->dropColumn('contact_channel');
            }
        });
    }
};
