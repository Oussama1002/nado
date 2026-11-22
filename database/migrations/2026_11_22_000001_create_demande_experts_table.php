<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_experts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 32);
            $table->string('ville')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('source', 32)->default('popup');      // popup | contact | …
            $table->enum('status', ['new', 'contacted', 'done', 'spam'])->default('new');
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_experts');
    }
};
