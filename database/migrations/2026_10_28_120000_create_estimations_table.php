<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('version')->nullable();
            $table->integer('year')->nullable();
            $table->date('first_registration')->nullable();
            $table->unsignedInteger('km')->nullable();
            $table->string('fuel')->nullable();
            $table->string('gearbox')->nullable();
            $table->unsignedInteger('power_cv')->nullable();
            $table->string('origin')->nullable();
            $table->decimal('estimated_value', 14, 2)->nullable();
            $table->decimal('range_min', 14, 2)->nullable();
            $table->decimal('range_max', 14, 2)->nullable();
            $table->string('confidence')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimations');
    }
};
