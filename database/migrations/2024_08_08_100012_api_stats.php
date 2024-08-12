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
        Schema::create('api_stats', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('api_source')->nullable();
            $table->string('api_url')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('table_name')->nullable();
            $table->string('has_request_completed')->default(0);
            $table->string('has_error')->nullable();
            $table->string('count')->nullable();
            $table->string('pages')->nullable();
            $table->string('page')->nullable();
            $table->string('per_page')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_stats');
    }
};
