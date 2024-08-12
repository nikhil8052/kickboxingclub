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
        Schema::create('time_clock_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('time_clock_id')->nullable();
            $table->string('start_datetime')->nullable();
            $table->string('end_datetime')->nullable();
            $table->string('duration')->nullable();
            $table->string('user_has_turf_access')->nullable();
            $table->json('relationships')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('location_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clock_shifts');
    }
};
