<?php

namespace App\Console\Commands;

use App\Models\Orders;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Exception;


class UpdateOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-orders';

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
        $latestDate = Orders::max('date_placed');

        if (!$latestDate) {
            // $this->info('No orders found.');
            // return;
            $latestDate = null;
        }

        if($latestDate != null){
            $startDate = Carbon::parse($latestDate)->format('Y-m-d\TH:i:s\Z');
        } else {
            $startDate = null;
        }
        // $startDate = null;
        $this->info("Latest date placed : $latestDate");

        $client = new Client();
        $url = env('API_URL') . 'orders';
        $accessToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
        $pageSize = 500;
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

                    $savedUsers = $this->saveOrderData($data['data']);

                    if ($savedUsers) {
                        $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                        if ($currentPage >= $totalPages) {
                            $hasMorePages = false;
                        } else {
                            $currentPage++;
                            sleep(15);
                        }
                    } else {
                        $this->error('Failed to save orders data.');
                        break;
                    }
                } else {
                    $this->error('API request failed with status code ' . $response->getStatusCode());
                    $hasMorePages = false;
                }
            } catch (Exception $e) {
                // $this->error('Error: ' . $e->getMessage());
                savelog("Error:","UpdateOrders", $e->getMessage());
                $hasMorePages = false;
            }
        }
    }

    public function saveOrderData($orders)
    {
        try {

            foreach($orders as $order){
                $olddata = Orders::where('order_id',$order['id'])->first();
                if(!$olddata){

                    $or = new Orders();
                    $or->type = $order['type'] ?? null;
                    $or->order_id = $order['id'] ?? null;

                    $or->contains_refund = $order['attributes']['contains_refund'] ?? null;
                    $or->currency = $order['attributes']['currency'] ?? null;

                    $or->deferred_item_total =$order['attributes']['deferred_item_count'];
                    $or->deferred_item_total =$order['attributes']['deferred_item_total'];
                    $or->deferred_item_total_incl_tax = $order['attributes']['deferred_item_total_incl_tax'];

                    $or->date_placed = $order['attributes']['date_placed'] ? Carbon::parse($order['attributes']['date_placed']) : null;

                    $placeddate =  isset( $order['attributes']['date_placed'])  ? Carbon::parse( $order['attributes']['date_placed']) : null;

                    $or->date_placed_copy = convertToUSATimezone($placeddate);

                    $or->discounts = json_encode($order['attributes']['discounts']) ?? null;

                    $or->has_deferred_payments =$order['attributes']['has_deferred_payments'];
                    $or->has_interac_payments =$order['attributes']['has_interac_payments'];
                    $or->is_past_refund_date = $order['attributes']['is_past_refund_date'];
                    $or->is_refundable = $order['attributes']['is_refundable'];

                    $or->location = $order['attributes']['location'] ?? null;
                    $or->net_total = $order['attributes']['net_total'] ?? null;

                    $or->nonrefundable_reasons = json_encode($order['attributes']['nonrefundable_reasons']) ?? null;

                    $or->number = $order['attributes']['number'] ?? null;

                    $or->payment_refund = json_encode($order['attributes']['payment_refunds']) ?? null;

                    if (isset($order['attributes']['payment_refunds'][0])) {
                        $refundSource = $order['attributes']['payment_refunds'][0];
                    
                        $or->refund_label = $refundSource['label'] ?? null;
                        $or->amount_refunded = $refundSource['amount_refunded'] ?? null;
                        $refunddate = isset($refundSource['date_created']) ? Carbon::parse($refundSource['date_created']) : null;
                        $or->refund_date_created = $refunddate ;
                        $or->refund_date_created_copy = convertToUSATimezone($refunddate);
                    } else {
                        $or->refund_label = null;
                        $or->amount_refunded = null;
                        $or->refund_date_created = null;
                    }

                    $or->payment_sources = json_encode($order['attributes']['payment_sources']) ?? null;

                    if (isset($order['attributes']['payment_sources'][0])) {
                        $paymentSource = $order['attributes']['payment_sources'][0];
                    
                        $or->payment_label = $paymentSource['label'] ?? null;
                        $or->amount_allocated = $paymentSource['amount_allocated'] ?? null;
                        $payementdate =  isset($paymentSource['date_created']) ? Carbon::parse($paymentSource['date_created'])  : null;
                        $or->date_created = $payementdate;
                        $or->date_created_copy = convertToUSATimezone($payementdate);
                        
                    } else {
                        $or->payment_label = null;
                        $or->amount_allocated = null;
                        $or->date_created = null;
                    }

                    $or->refund_sources = json_encode($order['attributes']['refund_sources']) ?? null;

                    $or->refund_subtotal = $order['attributes']['refund_subtotal'] ?? null;
                    $or->refund_total = $order['attributes']['refund_total'] ?? null;
                    $or->refund_total_tax = $order['attributes']['refund_total_tax'] ?? null;
                    $or->should_display_price_include_tax = $order['attributes']['should_display_price_include_tax'] ?? null;
                    $or->status = $order['attributes']['status'] ?? null;

                    // $or->purchased_items = json_encode($order['attributes']['purchased_items'] ?? null);
                    // if(isset($order['attributes']['purchased_items'][0])) {
                    //     $purchased_items_array = $order['attributes']['purchased_items'][0];
                    //     $or->purchased_item_id = $purchased_items_array['item_id'] ?? null;
                    // } else {
                    //     $or->purchased_item_id = null;
                    // }

                    $or->subtotal = $order['attributes']['subtotal'] ?? null;
                    $or->subtotal_excl_discounts = $order['attributes']['subtotal_excl_discounts'] ?? null;
                    $or->subtotal_incl_tax = $order['attributes']['subtotal_incl_tax'] ?? null;
                    $or->total =$order['attributes']['total'] ?? null;
                    $or->total_amount_refunded = $order['attributes']['total_amount_refunded'] ?? null;
                    $or->total_discount =$order['attributes']['total_discount'] ?? null;
                 
                    $or->total_tax = $order['attributes']['total_tax'] ?? null;
                    // $or->billing_address = $order['relationships']['billing_address'];
                    $or->broker_type = $order['relationships']['broker']['data']['type'] ?? null;
                    $or->broker_id = $order['relationships']['broker']['data']['id'] ?? null;
                    $or->cart_type = $order['relationships']['cart']['data']['type'] ?? null;
                    $or->cart_id = $order['relationships']['cart']['data']['id'] ?? null;
                    $or->fulfillment_partner_type =$order['relationships']['fulfillment_partner']['data']['type'] ?? null;
                    $or->fulfillment_partner_id = $order['relationships']['fulfillment_partner']['data']['id'] ?? null;
                    $or->user_type = $order['relationships']['user']['data']['type'] ?? null;
                    $or->user_id = $order['relationships']['user']['data']['id'] ?? null;
                    $or->order_line_type = $order['relationships']['order_lines']['data'][0]['type'] ?? null;
                    $or->order_line_id = $order['relationships']['order_lines']['data'][0]['id'] ?? null;
                    $or->originating_partner_type = $order['relationships']['originating_partner']['data']['type'] ?? null;
                    $or->originating_partner_id = $order['relationships']['originating_partner']['data']['id'] ?? null;
                    $or->save();
                }
            }
            return true;
        } catch(Exception $e) {
             $this->error('Error saving order data: ' . $e->getMessage());
             savelog("Error saving order data:","Updateorder", $e->getMessage());
             return false;
        }
       
    }
}
