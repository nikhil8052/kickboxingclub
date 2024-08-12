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
        Schema::create('freeze_memberships', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->bigInteger('menbership_freeze_id')->unique(); 
            $table->string('freeze_datetime')->nullable();
            $table->string('reactivation_datetime')->nullable();
            $table->json('relationships')->nullable();
            $table->string('membership_instance_type')->nullable();
            $table->string('membership_instance_id')->nullable();
            $table->string('broker_type')->nullable();
            $table->string('broker_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freeze_memberships');
    }
};
