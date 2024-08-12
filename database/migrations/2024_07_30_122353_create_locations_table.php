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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('location_id')->nullable();
            $table->string('name')->nullable();
            $table->string('legal_entity')->nullable();
            $table->string('timezone')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('address_sorting_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->boolean('user_has_turf_access')->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('primary_language')->nullable();
            $table->boolean('listed')->default(0);
            $table->string('currency_code')->nullable();
            $table->integer('geo_check_in_distance')->nullable();
            $table->string('gate_geo_check_in_by_distance')->nullable();
            $table->string('australian_business_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->json('formatted_address')->nullable(); 
            $table->string('use_tax_inclusive_pricing')->nullable();

            $table->string('region_type')->nullable();
            $table->string('region_id')->nullable();
            $table->json('classrooms')->nullable();
            $table->string('partner_type')->nullable();
            $table->string('partner_id')->nullable();
            $table->string('default_product_collection_type')->nullable();
            $table->string('default_product_collection_id')->nullable();
            $table->string('site_type')->nullable();
            $table->string('site_id')->nullable();
            $table->string('quick_sale_product_collection_type')->nullable();
            $table->string('quick_sale_product_collection_id')->nullable();
            $table->string('addons_product_collection_type')->nullable();
            $table->string('addons_product_collection_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
