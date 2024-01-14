<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('product_id')->primary();
            $table->boolean('is_active')->default(true);
            $table->string('title', 255);
            $table->string('description');
            $table->integer('stock_count');
            $table->decimal('price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->uuid('created_by')->index(); // Use uuid type for the foreign key
            $table->foreign('created_by')->references('user_id')->on('users');
            $table->foreignId('category_id')->constrained('product_categories', 'category_id');
            $table->timestamps();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
