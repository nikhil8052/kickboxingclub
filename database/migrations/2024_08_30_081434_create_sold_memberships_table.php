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
        Schema::create('sold_memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('membership_typeId')->nullable();
            $table->decimal('weekly_billing', 10, 2)->nullable();
            $table->decimal('monthly_billing', 10, 2)->nullable();
            $table->integer('employee_id')->nullable();
            $table->string('sold_by')->nullable();
            $table->integer('trial_id')->nullable(); 
            $table->boolean('status')->nullable();
            $table->timestamp('sold_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sold_memberships');
    }
};
