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
        Schema::create('membership_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('membership_name')->nullable();
            $table->bigInteger('transaction_amount')->nullable();
            $table->string('user_id')->default(0);
            $table->string('membership_instances_id')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('transaction_datetime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_transactions');
    }
};
