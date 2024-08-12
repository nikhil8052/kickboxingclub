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
          Schema::create('data_update_stats', function (Blueprint $table) {
            $table->id();
            $table->string('table_name')->nullable();
            $table->string('last_updated')->nullable();
            $table->string('has_error')->nullable();
            $table->string('app_logs_id')->nullable();
            $table->string('status')->nullable();
            $table->json('paylod')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_update_stats');
    }
};
