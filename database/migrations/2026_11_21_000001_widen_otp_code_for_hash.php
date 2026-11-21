<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Widen otp_code from 6 chars to 255 so it can store a bcrypt hash (60 chars).
            $table->string('otp_code', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('otp_code', 6)->nullable()->change();
        });
    }
};
