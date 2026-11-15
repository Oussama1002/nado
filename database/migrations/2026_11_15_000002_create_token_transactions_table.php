<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('token_transactions')) {
            Schema::create('token_transactions', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('client_id');

                // Signed: positive credits (bonus/purchase/refund/admin+),
                // negative debits (spend/admin-).
                $table->integer('amount');

                // What kind of movement this is.
                // signup_bonus | purchase | spend | refund | admin_adjust
                $table->string('type', 32);

                // Finer business reason for a spend/refund, e.g. "estimation",
                // "demande", "annonce_premium", "legalcheck", "boost", "lead", "banner".
                $table->string('reason', 64)->nullable();

                // Running balance AFTER this movement (audit convenience).
                $table->integer('balance_after');

                // Optional polymorphic link to the thing this movement is about
                // (an Estimation, Annonce, Demande, TokenPurchase, …).
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();

                // Free-form context (admin note, gateway payload snapshot, …).
                $table->json('meta')->nullable();

                $table->timestamps();

                $table->index('client_id');
                $table->index(['reference_type', 'reference_id']);
                $table->index('type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('token_transactions');
    }
};
