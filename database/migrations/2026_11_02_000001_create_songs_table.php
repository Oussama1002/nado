<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nado Radio — catalogue de titres diffusés sur la page /nado-radio.
 * Média géré depuis l'admin, même modèle que imgslider / videosads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('file_path');               // chemin relatif sur le disque public (songs/xxx.mp3)
            $table->string('cover_path')->nullable();   // pochette optionnelle (songs/covers/xxx.jpg)
            $table->string('duration')->nullable();     // libellé libre (ex. 3:45)
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
