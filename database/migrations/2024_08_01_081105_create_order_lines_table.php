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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_line_id')->unique();
            $table->json('transaction_data')->nullable(); 
            $table->string('transaction_type')->nullable(); 
            $table->string('transaction_id')->nullable(); 
            $table->string('membership_instance_id')->nullable(); 
            $table->boolean('is_payment_deferred')->default(false);
            $table->decimal('line_item_discount_amount', 10, 2)->default(0.0);
            $table->text('line_item_discount_note')->nullable();
            $table->decimal('line_subtotal', 10, 2)->default(0.0);
            $table->decimal('line_subtotal_incl_tax', 10, 2)->default(0.0);
            $table->decimal('line_subtotal_incl_tax_excl_line_item_discount', 10, 2)->default(0.0);
            $table->decimal('line_subtotal_excl_line_item_discount', 10, 2)->default(0.0);
            $table->decimal('line_subtotal_pre_discount', 10, 2)->default(0.0);
            $table->json('line_taxes')->nullable(); // JSON field for taxes
            $table->decimal('line_total', 10, 2)->default(0.0);
            $table->json('options')->nullable(); // JSON field for options
            $table->integer('quantity')->default(1);
            $table->boolean('should_display_price_include_tax')->default(false);
            $table->string('status')->default('Completed');
            $table->text('sub_title')->nullable();
            $table->string('title');
            $table->decimal('unit_subtotal', 10, 2)->default(0.0);
            $table->decimal('unit_total', 10, 2)->default(0.0);
            $table->text('variant_information')->nullable();
            $table->boolean('has_options')->default(true);
            $table->json('relationships')->nullable();
            $table->json('child_orders')->nullable();
            $table->string('order_type')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('product_type')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
