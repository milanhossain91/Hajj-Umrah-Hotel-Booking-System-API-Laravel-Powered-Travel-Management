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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('cost_price_without_tax', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('trade_price', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->decimal('wholesale', 10, 2);
            $table->string('wholesale_type');
            $table->boolean('disable_from_price_rules')->default(false);
            $table->boolean('allow_price_override_regardless_of_permissions')->default(false);
            $table->boolean('prices_include_tax')->default(false);
            $table->boolean('only_allow_items_to_be_sold_in_whole_numbers')->default(false);
            $table->boolean('change_cost_price_during_sale')->default(false);
            $table->boolean('override_default_commission')->default(false);
            $table->boolean('override_default_tax')->default(false);
            $table->boolean('is_editable_in_sale')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
