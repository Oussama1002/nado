<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('token_purchases')) {
            Schema::create('token_purchases', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('client_id');

                // How many tokens this top-up buys, and the MAD charged (1 token = 1 MAD,
                // but kept separate so a future promo/bonus can diverge the two).
                $table->integer('tokens');
                $table->decimal('amount_mad', 10, 2);

                // cmi | stripe | cash | bank_transfer
                $table->string('gateway', 32)->default('cmi');

                // pending | paid | failed | refunded | cancelled
                $table->string('status', 16)->default('pending');

                // Gateway correlation id (CMI oid, Stripe payment intent, …).
                $table->string('gateway_order_id')->nullable();

                // Raw gateway response / admin note, for audit.
                $table->json('meta')->nullable();

                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index('client_id');
                $table->index('status');
                $table->index('gateway_order_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('token_purchases');
    }
};
