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
        Schema::create('membership_instances', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('membership_id')->nullable();
            $table->string('adjustment_interval_count')->nullable();
            $table->string('adjustment_is_excluded_from_discounts')->nullable();
            $table->string('adjustment_renewal_rate')->nullable();
            $table->string('adjustment_renewal_rate_incl_tax')->nullable();
            $table->json('billing_cycles')->nullable();
            $table->string('billing_type')->nullable();
            $table->string('calculated_end_datetime')->nullable();
            $table->string('calculated_start_datetime')->nullable();
            $table->string('cancellation_datetime')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('commitment_length')->nullable();
            $table->string('end_date')->nullable();
            $table->string('freeze_datetime')->nullable();
            $table->string('freeze_reactivation_datetime')->nullable();
            $table->string('guest_usage_interval_limit')->nullable();
            $table->string('is_intro_offer')->nullable();
            $table->string('last_interval_remaining_guest_usage_count')->nullable();
            $table->string('last_interval_remaining_usage_count')->nullable();
            $table->string('membership_name')->nullable();
            $table->string('next_charge_date')->nullable();
            $table->string('next_charge_date_display')->nullable();
            
            $table->string('interval_start_date_display')->nullable();
            $table->string('payment_interval')->nullable();
            $table->string('payment_interval_end_date')->nullable();
            $table->string('payment_interval_length')->nullable();
            $table->string('payment_interval_start_date')->nullable();
            $table->string('purchase_date')->nullable();
            $table->string('purchase_date_copy')->nullable();

            $table->string('remaining_renewal_count')->nullable();
            $table->string('renewal_count')->nullable();
            $table->string('renewal_currency')->nullable();
            $table->string('renewal_limit')->nullable();
            $table->string('renewal_rate')->nullable();
            $table->string('renewal_rate_incl_tax')->nullable();

            $table->string('scheduled_end_datetime')->nullable();
            $table->string('should_display_price_include_tax')->nullable();
            $table->string('start_date')->nullable();
            $table->string('status')->nullable();
            $table->string('usage_interval_limit')->nullable();

            $table->json('relationships')->nullable();
            
            $table->string('current_membership_transaction_type')->nullable();
            $table->string('current_membership_transaction_id')->nullable();

            $table->string('membership_type')->nullable();
            $table->string('membership_type_id')->nullable();

            $table->string('membership_freeze_type')->nullable();
            $table->string('membership_freeze_id')->nullable();

            $table->json('events')->nullable();

            $table->string('membership_product_type')->nullable();
            $table->string('membership_product_id')->nullable();

            $table->string('purchase_location_type')->nullable();
            $table->string('purchase_location_id')->nullable();

            $table->string('user_type')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_instances');
    }
};
