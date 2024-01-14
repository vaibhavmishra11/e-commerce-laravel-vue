<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_transactions', function (Blueprint $table) {
            $table->uuid('TID')->primary();
            $table->uuid('original_transaction_id')->nullable();
            $table->decimal('amount', 10, 2);

            // Foreign key references
            $table->uuid('product_id')->index();
            $table->uuid('purchased_by')->index();
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->foreign('purchased_by')->references('user_id')->on('users');

            // Other fields
            $table->enum('transaction_type', ['purchase', 'refund']);
            $table->enum('transaction_status', ['pending', 'completed', 'failure']);
            $table->uuid('payment_purchased_id')->nullable();
            $table->enum('payment_method', ['cash', 'wallet', 'card', 'net-banking', 'UPI']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_transactions');
    }
};
