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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('credit_id')->nullable();
            $table->string('name')->nullable();
            $table->string('guest_usage')->nullable();
            $table->string('is_active')->nullable();
            $table->string('location_availability_override')->nullable();
            $table->string('user_has_any_locations')->nullable();
            $table->string('user_has_all_locations')->nullable();
            $table->string('currency_codes')->nullable();
            $table->string('is_live_stream')->nullable();
            $table->string('ding_exempt')->nullable();
            $table->json('relationships')->nullable();
            $table->json('credit_slots_id')->nullable();
            $table->json('booking_windows_id')->nullable();
            $table->json('late_cancel_windows_id')->nullable();
            $table->json('locations_id')->nullable();
            $table->json('class_session_tags_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
