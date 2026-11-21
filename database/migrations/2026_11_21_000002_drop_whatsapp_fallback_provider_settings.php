<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->whereIn('key', [
            'whatsapp.ultramsg_instance',
            'whatsapp.ultramsg_token',
            'whatsapp.callmebot_apikey',
        ])->delete();
    }

    public function down(): void
    {
        // No-op: the SettingsSeeder re-creates whatever set it maintains.
    }
};
