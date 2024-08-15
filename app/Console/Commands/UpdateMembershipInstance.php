<?php

namespace App\Console\Commands;

use App\Models\MembershipInstances;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;

class UpdateMembershipInstance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-membership-instance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $latestDate = MembershipInstances::max('created_at');

        if (!$latestDate) {
            // $this->info('No Memberships found.');
            // return;
            $latestDate = null;
        }
        if($latestDate != null) {
            $startDate = Carbon::parse($latestDate)->format('Y-m-d\TH:i:s\Z');
        } else {
            $startDate = null;
        }

        $this->info("Latest date placed : $latestDate");

        $client = new Client();
        $url = env('API_URL') . 'membership_instances';
        $accessToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
        $pageSize = 100; 
        $hasMorePages = true;

        while ($hasMorePages) {
            try {
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                    'query' => [
                        'min_updated_datetime' => $startDate,
                        'page' => $currentPage,
                        'page_size' => $pageSize,
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $body = $response->getBody()->getContents();
                    $data = json_decode($body, true);

                    $savedUsers = $this->saveMembership($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(30);
                        }
                    } else {
                        $this->error('Failed to save memberships data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateMemberships", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveMembership($orders)
    {
        try {
            foreach($orders as $order){
                $olddata = MembershipInstances::where('membership_id',$order['id'])->first();
                if(!$olddata){
                    $attributes = $order['attributes'] ?? [];
                    $relationships = $order['relationships'] ?? [];

                    $or = new MembershipInstances();
                    $or->type = $order['type'] ?? null;
                    $or->membership_id = $order['id'] ?? null;
                    $or->adjustment_interval_count = $order['attributes']['adjustment_interval_count'] ?? null;
                    $or->adjustment_is_excluded_from_discounts = $order['attributes']['adjustment_is_excluded_from_discounts'] ?? null;
                    $or->adjustment_renewal_rate = $order['attributes']['adjustment_renewal_rate'] ?? null;
                    $or->adjustment_renewal_rate_incl_tax = $order['attributes']['adjustment_renewal_rate_incl_tax'] ?? null;

                    $or->billing_cycles = json_encode($order['attributes']['billing_cycles']) ?? null;
                    $or->billing_type = $order['attributes']['billing_type'] ?? null;
                    $or->calculated_end_datetime = $order['attributes']['calculated_end_datetime'] ? Carbon::parse($order['attributes']['calculated_end_datetime']) : null;
                    $or->calculated_start_datetime = $order['attributes']['calculated_start_datetime'] ? Carbon::parse($order['attributes']['calculated_start_datetime']) : null;
                    // $or->cancellation_datetime = Carbon::parse($order['attributes']['cancellation_datetime']) ?? null;
                    $or->cancellation_datetime = $order['attributes']['cancellation_datetime'] ? Carbon::parse($order['attributes']['cancellation_datetime']) : null;
                    $or->cancellation_reason = $order['attributes']['cancellation_reason'] ?? null;
                    $or->commitment_length = $order['attributes']['commitment_length'] ?? null;
                    $or->end_date = $order['attributes']['end_date'] ? Carbon::parse($order['attributes']['end_date']) : null;
                    $or->freeze_datetime = $order['attributes']['freeze_datetime'] ? Carbon::parse($order['attributes']['freeze_datetime']) : null;
                    $or->freeze_reactivation_datetime =$order['attributes']['freeze_reactivation_datetime'] ?? null;
                    $or->guest_usage_interval_limit = $order['attributes']['guest_usage_interval_limit'] ?? null;
                    $or->is_intro_offer =$order['attributes']['is_intro_offer'] ?? null;
                    $or->last_interval_remaining_guest_usage_count = $order['attributes']['last_interval_remaining_guest_usage_count'];
                    $or->last_interval_remaining_usage_count =$order['attributes']['last_interval_remaining_usage_count'];
                    $or->membership_name = $order['attributes']['membership_name'];
                    $or->next_charge_date = $order['attributes']['next_charge_date'] ?? null;
                    $or->next_charge_date_display = $order['attributes']['next_charge_date_display'];

                    $or->interval_start_date_display = $order['attributes']['interval_start_date_display'] ?? null;
                    $or->payment_interval =$order['attributes']['payment_interval'] ?? null;
                    $or->payment_interval_end_date = $order['attributes']['payment_interval_end_date'] ?? null;
                    $or->payment_interval_length =$order['attributes']['payment_interval_length'] ?? null;
                    $or->payment_interval_start_date = $order['attributes']['payment_interval_start_date'];
                    $or->purchase_date = $order['attributes']['purchase_date'] ? Carbon::parse($order['attributes']['purchase_date']) : null;
                    $or->purchase_date_copy = $order['attributes']['purchase_date'] ? Carbon::parse($order['attributes']['purchase_date']) : null;
                    $or->remaining_renewal_count = $order['attributes']['remaining_renewal_count'];
                    $or->renewal_count = $order['attributes']['renewal_count'] ?? null;
                    $or->renewal_currency = $order['attributes']['renewal_currency'] ?? null;
                    $or->renewal_limit = $order['attributes']['renewal_limit'] ?? null;
                    $or->renewal_rate = $order['attributes']['renewal_rate'] ?? null;
                    $or->renewal_rate_incl_tax = $order['attributes']['renewal_rate_incl_tax'] ?? null;

                    $or->scheduled_end_datetime = $order['attributes']['scheduled_end_datetime'] ?? null;
                    $or->should_display_price_include_tax = $order['attributes']['should_display_price_include_tax'] ?? null;
                    $or->start_date = $order['attributes']['start_date'] ? Carbon::parse($order['attributes']['start_date']) : null;
                    $or->status = $order['attributes']['status'] ?? null;
                    $or->usage_interval_limit = $order['attributes']['usage_interval_limit'] ?? null;

                    $or->relationships = json_encode($order['relationships']) ?? null;
                    $or->current_membership_transaction_type = $order['relationships']['current_membership_transaction']['data']['type'] ?? null;
                    $or->current_membership_transaction_id = $order['relationships']['current_membership_transaction']['data']['id'] ?? null;

                    $or->membership_type = $order['relationships']['membership']['data']['type'] ?? null;
                    $or->membership_type_id = $order['relationships']['membership']['data']['id'] ?? null;

                    $or->membership_freeze_type = $order['relationships']['membership_freeze']['data']['type'] ?? null;
                    $or->membership_freeze_id = $order['relationships']['membership_freeze']['data']['id'] ?? null;

                    $or->events = json_encode($order['relationships']['events']['data']) ?? null;

                    $or->membership_product_id = $order['relationships']['membership_product']['data']['id'] ?? null;
                    $or->membership_product_type =$order['relationships']['membership_product']['data']['type'] ?? null;

                    $or->purchase_location_id = $order['relationships']['purchase_location']['data']['id'] ?? null;
                    $or->purchase_location_type = $order['relationships']['purchase_location']['data']['type'] ?? null;

                    $or->user_id = $order['relationships']['user']['data']['id'] ?? null;
                    $or->user_type = $order['relationships']['user']['data']['type'] ?? null;
                
                    $or->save();
                }
            }
            return true;
        } catch(Exception $e) {
             $this->error('Error saving memberships data: ' . $e->getMessage());
             savelog("Error saving memberships data:","UpdateMemberships", $e->getMessage());
             return false;
        }
       
    }
}
