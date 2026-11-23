<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_messages', 'media_path')) {
                // Chemin relatif sur le disque public (storage/app/public/whatsapp/…).
                $table->string('media_path')->nullable()->after('media_id');
            }
            if (! Schema::hasColumn('whatsapp_messages', 'media_mime')) {
                $table->string('media_mime', 100)->nullable()->after('media_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_messages', 'media_mime'))  $table->dropColumn('media_mime');
            if (Schema::hasColumn('whatsapp_messages', 'media_path'))  $table->dropColumn('media_path');
        });
    }
};
