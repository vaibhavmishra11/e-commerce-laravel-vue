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
        Schema::create('purchase_product_information', function (Blueprint $table) {
            $table->uuid('product_id')->index();
            $table->boolean('is_active')->default(true);
            $table->string('title', 255);
            $table->text('description');
            $table->integer('stock_count');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // Foreign key references
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->uuid('purchased_by')->index();
            $table->foreign('purchased_by')->references('user_id')->on('users');
            $table->foreignId('category_id')->constrained('product_categories', 'category_id');
            $table->uuid('TID')->index();
            $table->foreign('TID')->references('TID')->on('purchase_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_product_information');
    }
};
