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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('name');
            $table->string('product_id')->unique();
            $table->string('tags')->nullable();
            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->text('description')->nullable();
            $table->string('batch_no')->nullable();
            $table->unsignedBigInteger('generic_id')->nullable();
            $table->boolean('is_ecommerce_item')->default(false);
            $table->boolean('is_barcoded')->default(false);
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
