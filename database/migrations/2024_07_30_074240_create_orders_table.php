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

            $table->boolean('contains_refund')->nullable();
            $table->string('currency')->nullable();
            $table->string('date_placed')->nullable();
            $table->date('date_placed_copy')->nullable();
            $table->string('deferred_item_count')->nullable();
            $table->string('deferred_item_total')->nullable();
            $table->string('deferred_item_total_incl_tax')->nullable();

            $table->string('discounts')->nullable();

            $table->boolean('has_deferred_payments')->nullable();
            $table->boolean('has_interac_payments')->nullable();
            $table->boolean('is_past_refund_date')->nullable();
            $table->boolean('is_refundable')->nullable();

            $table->string('location')->nullable();
            $table->string('net_total')->nullable();

            $table->text('nonrefundable_reasons')->nullable();

            $table->string('number')->nullable();

            $table->text('payment_refund')->nullable();
            $table->string('refund_label')->nullable();
            $table->decimal('amount_refunded', 10, 2)->nullable();
            $table->string('refund_date_created')->nullable();
            $table->date('refund_date_created_copy')->nullable();

            $table->text('payment_sources')->nullable();
            $table->string('payment_label')->nullable();
            $table->decimal('amount_allocated', 10, 2)->nullable();
            $table->string('date_created')->nullable();
            $table->date('date_created_copy')->nullable();

            $table->text('refund_sources')->nullable();

            $table->string('refund_subtotal')->nullable();
            $table->string('refund_total')->nullable();
            $table->string('refund_total_tax')->nullable();
            $table->boolean('should_display_price_include_tax')->nullable();

            $table->string('status')->nullable();

            $table->string('subtotal')->nullable();
            $table->string('subtotal_excl_discounts')->nullable();
            $table->string('subtotal_incl_tax')->nullable();

            $table->text('summary')->nullable();
            $table->text('summary_nonrefunded_items')->nullable();
            $table->text('taxes')->nullable();

            $table->string('total')->nullable();
            $table->string('total_amount_refunded')->nullable();
            $table->string('total_discount')->nullable();
            $table->string('total_tax')->nullable();

            $table->text('purchased_items')->nullable();
            $table->string('purchased_item_id')->nullable();

            $table->string('billing_address')->nullable();
            $table->string('broker_type')->nullable();
            $table->string('broker_id')->nullable();
            $table->string('cart_type')->nullable();
            $table->string('cart_id')->nullable();
            $table->string('fulfillment_partner_type')->nullable();
            $table->string('fulfillment_partner_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('user_id')->nullable();
            $table->string('order_line_type')->nullable();
            $table->string('order_line_id')->nullable();
            $table->string('originating_partner_type')->nullable();
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
