<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('wa_phone', 32)->unique();      // +212XXXXXXXXX (E.164)
            $table->string('display_name')->nullable();    // name from Meta profile
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('last_inbound_at')->nullable(); // used for the 24h window check
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamps();

            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
