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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->bigInteger('employee_id')->unique(); 
            $table->string('payroll_id')->nullable();
            $table->string('is_active')->nullable();
            $table->string('can_chat')->nullable();
            $table->string('is_beta_user')->nullable();
            $table->json('relationships')->nullable();
            
            $table->string('user_type')->nullable();
            $table->string('user_id')->nullable();
            $table->string('recent_location_type')->nullable();
            $table->string('recent_location_id')->nullable();
            $table->string('public_profile_type')->nullable();
            $table->string('public_profile_id')->nullable();
            $table->json('groups')->nullable();
            $table->json('turfs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
