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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('order_id')->nullable();
            $table->string('contains_refund')->nullable();
            $table->string('currency')->nullable();
            $table->string('date_placed')->nullable();
            $table->string('deferred_item_count')->nullable();
            $table->string('deferred_item_total')->nullable();
            $table->string('deferred_item_total_incl_tax')->nullable();
            $table->string('discounts')->nullable();
            $table->string('location')->nullable();
            $table->string('net_total')->nullable();
            $table->string('number')->nullable();
            $table->string('payment_sources')->nullable();
            $table->string('refund_subtotal')->nullable();
            $table->string('refund_total')->nullable();
            $table->string('refund_total_tax')->nullable();
            $table->string('status')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('subtotal_excl_discounts')->nullable();
            $table->string('subtotal_incl_tax')->nullable();
            $table->string('total')->nullable();
            $table->string('total_amount_refunded')->nullable();
            $table->string('total_discount')->nullable();
            $table->string('total_tax')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('broker_type')->nullable();
            $table->string('broker_id')->nullable();
            $table->string('cart_types')->nullable();
            $table->string('cart_id')->nullable();
            $table->string('fulfillment_partner_types')->nullable();
            $table->string('fulfillment_partner_id')->nullable();
            $table->string('user_types')->nullable();
            $table->string('user_id')->nullable();
            $table->string('order_lines_types')->nullable();
            $table->string('order_lines_id')->nullable();
            $table->string('originating_partner_types')->nullable();
            $table->string('originating_partner_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
