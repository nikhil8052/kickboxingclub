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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('group_id')->nullable();
            $table->string('group_name')->nullable();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->string('user_can_assign_group')->nullable();
            $table->string('public')->nullable();
            $table->json('relationships')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
