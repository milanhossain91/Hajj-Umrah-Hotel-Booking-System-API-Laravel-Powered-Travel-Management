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
        Schema::create('purchase_bonus_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('pack_size_id')->nullable();
            $table->decimal('tp', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->integer('received_quantity')->default(0);
            $table->integer('quantity')->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('size')->nullable();
            $table->string('generic_name')->nullable();
            $table->string('serial')->nullable();
            $table->integer('stock')->nullable();
            $table->decimal('cost_price_preview', 15, 2)->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_bonus_products');
    }
};
