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
        Schema::create('all_users', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->bigInteger('user_id')->unique(); 
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address_sorting_code')->nullable();
            $table->string('country')->nullable();
            $table->string('full_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();
            $table->boolean('signed_waiver')->default(0);
            $table->string('waiver_signed_datetime')->nullable();
            $table->string('date_joined')->nullable();
            $table->boolean('marketing_opt_in')->default(0);
            $table->boolean('is_opted_in_to_sms')->default(0);
            $table->boolean('has_vip_tag_cache')->default(0);
            $table->boolean('apply_account_balance_to_fees')->default(0);
            $table->boolean('is_minimal')->default(0);
            $table->json('permissions')->nullable();
            $table->decimal('account_balance', 8, 2)->default(0.00);
            $table->json('account_balances')->nullable();
            $table->boolean('third_party_sync')->default(0);
            $table->integer('completed_class_count')->default(0);
            $table->string('company_name')->nullable();
            $table->string('archived_at')->nullable();
            $table->boolean('is_external_user')->default(0);
            $table->bigInteger('merged_into_id')->nullable();
            $table->json('waivers')->nullable();
            $table->boolean('has_unsigned_waivers')->default(0);
            $table->json('marketing_logs')->nullable();
            $table->json('formatted_address')->nullable();
            $table->json('required_legal_documents')->nullable();
            $table->string('pronouns')->nullable();
            $table->string('search_priority_category')->nullable();
            $table->json('relationships')->nullable();
            $table->json('last_region')->nullable();
            $table->string('home_location_type')->nullable();
            $table->string('home_location_id')->nullable();
            $table->string('tags')->nullable();
            $table->json('profile_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_users');
    }
};
