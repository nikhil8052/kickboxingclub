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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('discount_id')->nullable();
            $table->string('name')->nullable();
            $table->string('start_datetime')->nullable();
            $table->string('end_datetime')->nullable();
            $table->json('codes')->nullable();
            $table->string('benefit_type')->nullable();
            $table->string('benefit_proxy_class')->nullable();
            $table->decimal('benefit_value', 10, 2)->nullable();
            $table->string('benefit_currency')->nullable();
            $table->string('max_global_applications')->nullable();
            $table->string('max_user_applications')->nullable();
            $table->string('benefit_includes_all_products')->nullable();
            $table->string('offer_type')->nullable();
            $table->json('benefit_excluded_products')->nullable();
            $table->json('benefit_included_product_classes')->nullable();
            $table->json('benefit_included_products')->default(0);
            $table->json('condition_membership_contracts')->default(0);
            $table->boolean('is_active')->nullable();
            $table->string('user_segment_type')->nullable();
            $table->string('condition_user_tag')->nullable();
            $table->json('turf')->nullable();
            $table->string('global_turf_enabled')->nullable();
            $table->string('global_turf_can_assign')->nullable();
            $table->json('regions')->nullable();
            $table->boolean('user_has_any_locations')->default(0);
            $table->boolean('user_has_all_locations')->default(0);
            $table->json('relationships')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
