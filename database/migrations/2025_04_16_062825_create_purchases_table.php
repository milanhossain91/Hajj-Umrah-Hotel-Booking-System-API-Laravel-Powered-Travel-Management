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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code')->unique();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('getis_percent', 10, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->unsignedBigInteger('mrr_id')->nullable();
            $table->decimal('sub_total', 15, 2)->nullable();
            $table->decimal('total_trade_price', 15, 2)->nullable();
            $table->decimal('total_vat', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('amount_due', 15, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('paid_amount', 15, 2)->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->decimal('discount_entire_purchase', 15, 2)->default(0);
            $table->decimal('discount_all_percent', 10, 2)->default(0);
            $table->string('invoice_no')->nullable();
            $table->string('file')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
