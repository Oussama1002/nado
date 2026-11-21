<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('whatsapp_conversations')
                ->cascadeOnDelete();
            $table->enum('direction', ['in', 'out']);
            $table->string('wamid')->nullable()->index();   // Meta message id
            $table->string('type', 32)->default('text');    // text|image|audio|template|…
            $table->text('body')->nullable();               // plain text or caption
            $table->string('media_id')->nullable();         // Meta media id for attachments
            $table->string('status', 24)->nullable();       // sent|delivered|read|failed
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
