<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();         // PayMob order ID
            $table->string('transaction_id')->nullable();   // PayMob transaction ID
            $table->unsignedBigInteger('amount_cents');
            $table->boolean('success')->default(false);
            $table->string('currency')->default('EGP');
            $table->string('card_token')->nullable();       // Saved card token
            $table->json('raw_data');                       // Full webhook payload
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymob_transactions');
    }
};
