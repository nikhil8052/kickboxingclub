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
        Schema::create('child_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_discountable')->default(true);
            $table->string('slug')->unique();
            $table->string('date_created')->nullable();
            $table->string('date_updated')->nullable();
            $table->json('supported_currencies')->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('default_inventoriable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('user_has_any_locations')->default(true);
            $table->boolean('user_has_all_locations')->default(true);
            $table->boolean('is_live_stream')->default(false);
            $table->boolean('is_first_timer_only')->default(false);
            $table->boolean('is_intro_offer')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->json('pricing')->nullable();
            $table->decimal('pricing_price', 10, 2)->nullable();
            $table->string('pricing_currency')->nullable();
            $table->boolean('pricing_enabled')->nullable();
            $table->string('upc')->nullable();
            $table->text('sub_title')->nullable();
            $table->text('sku')->nullable();
            $table->string('product_class_type')->nullable();
            $table->string('product_class_id')->nullable();
            $table->string('parent_type')->nullable();
            $table->string('parent_id')->nullable();
            $table->json('relationships')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_products');
    }
};
