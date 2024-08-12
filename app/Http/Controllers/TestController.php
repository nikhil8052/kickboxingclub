<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Orders;
use App\Models\Locations;
use App\Models\MembershipInstances;
use App\Models\AllUsers;
use App\Models\Employees;
use App\Models\FreezeMemberships;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ChildProduct;
use App\Models\OrderLine;
use Carbon\Carbon;

class TestController extends Controller
{
    public function testapi($api)
    {
        $client = new Client();
        $url = env('API_URL').$api;
        $accessToken = env('API_ACCESS_TOKEN');

        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d\TH:i:s\Z');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d\TH:i:s\Z');

        // $date = Carbon::create(2024, 7, 29, 7, 0, 0, 'UTC')->format('Y-m-d\TH:i:s\Z');

        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . env('API_ACCESS_TOKEN'),
            ],
            'query' => [
                // 'min_updated_datetime' => $startDate,
                // 'max_updated_datetime' => $startDate,
                // 'status' =>'Payment Failure',
                // 'page' => 1,
                'page_size' => 100,
            ],
        ]);  
      

        $statuscode = $response->getStatusCode();

        if($statuscode == 200){
            $body = $response->getBody()->getContents();
            return $body;
            // $data = json_decode($body,true);
            // echo "<pre>";
            // print_r($data);
            // die();
        }
    }

    // public function testapi($api)
    // {
    //     $client = new Client();
    //     $url = env('API_URL') . $api;
    //     $accessToken = env('API_ACCESS_TOKEN');

        // $currentPage = 2;
        // $pageSize = 500;
        // $hasMorePages = true;

        // while ($hasMorePages) {
        //     $response = $client->request('GET', $url, [
        //         'headers' => [
        //             'Authorization' => 'Bearer ' . $accessToken,
        //         ],
        //         'query' => [
        //             'page' => $currentPage,
        //             'page_size' => $pageSize,
        //         ],
        //     ]);

        //     $statuscode = $response->getStatusCode();

        //     if ($statuscode == 200) {
        //         $body = $response->getBody()->getContents();
        //         $data = json_decode($body, true);
               
        //         $savedOrders =  $this->saveOrderLines($data['data']);

        //         if($savedOrders) {
        //             $totalPages = $data['meta']['pagination']['pages'] ?? 0;
        //             if ($currentPage >= $totalPages) {
        //                 $hasMorePages = false;
        //             } else {
        //                 $hasMorePages = false;
        //                 $currentPage++;
        //             }
        //         } else {
                    
        //             die();
        //         }
                
        //     } else {
        //         $hasMorePages = false;
        //     }
        // }

    //     return "Order Lines Added in database";
    // }


    public function saveOrderLines($orderlines)
    {
        try {
            foreach($orderlines as $lineData){


                $line = new OrderLine();
                $line->order_line_id = $lineData['id'];
                $line->transaction_data = json_encode($lineData['attributes']['transaction_data']);
                $line->transaction_type = $lineData['attributes']['transaction_data'][0]['transaction_type'] ?? null;
                $line->transaction_id = $lineData['attributes']['transaction_data'][0]['transaction_id'] ?? null;
                $line->membership_instance_id = $lineData['attributes']['transaction_data'][0]['membership_instance_id'] ?? null;
                $line->is_payment_deferred = $lineData['attributes']['is_payment_deferred'];
                $line->line_item_discount_amount = $lineData['attributes']['line_item_discount_amount'];
                $line->line_item_discount_note = $lineData['attributes']['line_item_discount_note'];
                $line->line_subtotal = $lineData['attributes']['line_subtotal'];
                $line->line_subtotal_incl_tax = $lineData['attributes']['line_subtotal_incl_tax'];
                $line->line_subtotal_incl_tax_excl_line_item_discount = $lineData['attributes']['line_subtotal_incl_tax_excl_line_item_discount'];
                $line->line_subtotal_excl_line_item_discount = $lineData['attributes']['line_subtotal_excl_line_item_discount'];
                $line->line_subtotal_pre_discount = $lineData['attributes']['line_subtotal_pre_discount'];
                $line->line_taxes = json_encode($lineData['attributes']['line_taxes']);
                $line->line_total = $lineData['attributes']['line_total'];
                $line->options = json_encode($lineData['attributes']['options']);
                $line->quantity = $lineData['attributes']['quantity'];
                $line->should_display_price_include_tax = $lineData['attributes']['should_display_price_include_tax'];
                $line->status = $lineData['attributes']['status'];
                $line->sub_title = $lineData['attributes']['sub_title'];
                $line->title = $lineData['attributes']['title'];
                $line->unit_subtotal = $lineData['attributes']['unit_subtotal'];
                $line->unit_total = $lineData['attributes']['unit_total'];
                $line->variant_information = $lineData['attributes']['variant_information'];
                $line->has_options = $lineData['attributes']['has_options'];
                $line->child_orders = json_encode($lineData['relationships']['child_orders']['data']);
                $line->order_id = $lineData['relationships']['order']['data']['id'];
                $line->product_id = $lineData['relationships']['product']['data']['id'];
                $line->order_type = $lineData['relationships']['order']['data']['type'];
                $line->product_type = $lineData['relationships']['product']['data']['type'];
                $line->relationships = json_encode($lineData['relationships']);
                $line->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function saveProductsData($products)
    {
        try {
            foreach($products as $productData){


                $product = new Product();
                $product->id = $productData['id'];
                $product->title = $productData['attributes']['title'];
                $product->description = $productData['attributes']['description'];
                $product->is_discountable = $productData['attributes']['is_discountable'];
                $product->slug = $productData['attributes']['slug'];
                $product->date_created = $productData['attributes']['date_created'];
                $product->date_updated = $productData['attributes']['date_updated'];
                $product->supported_currencies = json_encode($productData['attributes']['supported_currencies']);
                $product->options = json_encode($productData['attributes']['options']);
                $product->is_public = $productData['attributes']['is_public'];
                $product->default_inventoriable = $productData['attributes']['default_inventoriable'];
                $product->is_active = $productData['attributes']['is_active'];
                $product->user_has_any_locations = $productData['attributes']['user_has_any_locations'];
                $product->user_has_all_locations = $productData['attributes']['user_has_all_locations'];
                $product->is_live_stream = $productData['attributes']['is_live_stream'];
                $product->is_first_timer_only = $productData['attributes']['is_first_timer_only'];
                $product->is_intro_offer = $productData['attributes']['is_intro_offer'];
                $product->min_price = $productData['attributes']['min_price'];
                $product->max_price = $productData['attributes']['max_price'];
                $product->children_count = $productData['attributes']['children_count'];
                $product->anonymously_purchasable = $productData['attributes']['anonymously_purchasable'];
                $product->product_class_id = $productData['relationships']['product_class']['data']['id'];
                $product->location = json_encode($productData['attributes']['locations']);
                $product->relationships = json_encode($productData['relationships']);
                $product->vendor = $productData['attributes']['vendor'];
                $product->sub_category = $productData['attributes']['sub_category'];

                $product->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function saveChildProductsData($products)
    {
        try {
            foreach($products as $productData){


                $product = new ChildProduct();
                $product->id = $productData['id'];
                $product->title = $productData['attributes']['title'];
                $product->description = $productData['attributes']['description'];
                $product->is_discountable = $productData['attributes']['is_discountable'];
                $product->slug = $productData['attributes']['slug'];
                $product->date_created = $productData['attributes']['date_created'];
                $product->date_updated = $productData['attributes']['date_updated'];
                $product->supported_currencies = json_encode($productData['attributes']['supported_currencies']);
                $product->options = json_encode($productData['attributes']['options']);
                $product->is_public = $productData['attributes']['is_public'];
                $product->default_inventoriable = $productData['attributes']['default_inventoriable'];
                $product->is_active = $productData['attributes']['is_active'];
                $product->user_has_any_locations = $productData['attributes']['user_has_any_locations'];
                $product->user_has_all_locations = $productData['attributes']['user_has_all_locations'];
                $product->is_live_stream = $productData['attributes']['is_live_stream'];
                $product->is_first_timer_only = $productData['attributes']['is_first_timer_only'];
                $product->is_intro_offer = $productData['attributes']['is_intro_offer'];
                $product->price = $productData['attributes']['price'];
                $product->pricing = json_encode($productData['attributes']['pricing']);
                $product->upc = $productData['attributes']['upc'];
                $product->sub_title = $productData['attributes']['sub_title'];
                $product->sku = $productData['attributes']['sku'];
                $product->relationships = json_encode($productData['relationships']);
                $product->product_class_id = $productData['relationships']['product_class']['data']['id'];
                $product->product_class_type = $productData['relationships']['product_class']['data']['type'];
                $product->parent_type = $productData['relationships']['product_class']['data']['type'];
                $product->parent_id = $productData['relationships']['parent']['data']['id'];
    
                $product->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function savediscountData($discounts)
    {
        try {
            foreach($discounts as $data) {
                $attributes = $data['attributes'];
    
                // Create and save the main Discount entry
                $discount = new Discount();
                $discount->name = $attributes['name'];
                $discount->start_datetime = $attributes['start_datetime'];
                $discount->end_datetime = $attributes['end_datetime'];
                $discount->benefit_type = $attributes['benefit_type'];
                $discount->benefit_proxy_class = $attributes['benefit_proxy_class'];
                $discount->benefit_value = $attributes['benefit_value'];
                $discount->benefit_currency = $attributes['benefit_currency'];
                $discount->offer_type = $attributes['offer_type'];
                $discount->is_active = $attributes['is_active'];
                $discount->user_segment_type = $attributes['user_segment_type'];
                $discount->user_has_any_locations = $attributes['user_has_any_locations'];
                $discount->user_has_all_locations = $attributes['user_has_all_locations'];
                $discount->save();
    
                // Save related discount codes
                foreach($attributes['codes'] as $code) {
                    $discountCode = new DiscountCode();
                    $discountCode->discount_id = $discount->id;
                    $discountCode->code = $code;
                    $discountCode->save();
                }
    
                // Save related discount products
                foreach($attributes['benefit_included_products'] as $product) {
                    $discountProduct = new DiscountProduct();
                    $discountProduct->discount_id = $discount->id;
                    $discountProduct->product_title = $product['title'];
                    $discountProduct->product_id = $product['id'];
                    $discountProduct->product_class_id = $product['product_class']['id'];
                    $discountProduct->product_class_name = $product['product_class']['name'];
                    $discountProduct->product_class_slug = $product['product_class']['slug'];
                    $discountProduct->save();
                }
    
                // Save related discount regions and locations
                foreach($attributes['turf']['regions'] as $region) {
                    $discountRegion = new DiscountRegion();
                    $discountRegion->discount_id = $discount->id;
                    $discountRegion->region_name = $region['name'];
                    $discountRegion->region_id = $region['id'];
                    $discountRegion->region_enabled = $region['enabled'];
                    $discountRegion->region_can_assign = $region['can_assign'];
                    $discountRegion->save();
    
                    // Save locations within the region
                    foreach($region['locations'] as $location) {
                        $discountLocation = new DiscountLocation();
                        $discountLocation->region_id = $discountRegion->id;
                        $discountLocation->location_name = $location['name'];
                        $discountLocation->location_id = $location['id'];
                        $discountLocation->currency_code = $location['currency_code'];
                        $discountLocation->location_enabled = $location['enabled'];
                        $discountLocation->location_can_assign = $location['can_assign'];
                        $discountLocation->save();
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
    
    public function saveOrderDate($orders)
    {
        try {
            foreach($orders as $order){

                $or = new Orders();
                $or->type = $order['type'] ?? null;
                $or->order_id = $order['id'] ?? null;
                $or->contains_refund = $order['attributes']['contains_refund'] ?? null;
                $or->currency = $order['attributes']['currency'] ?? null;
                $or->date_placed = $order['attributes']['date_placed'] ?? null;
                // $or->discounts = $order['attributes']['contains_refund'];
                $or->location = $order['attributes']['location'] ?? null;
                $or->net_total = $order['attributes']['net_total'] ?? null;
                $or->number = $order['attributes']['number'] ?? null;
                // $or->payment_sources = $order['attributes']['payment_sources'] ?? null;
                $or->refund_subtotal = $order['attributes']['refund_subtotal'] ?? null;
                $or->refund_total = $order['attributes']['refund_total'] ?? null;
                $or->refund_total_tax = $order['attributes']['refund_total_tax'] ?? null;
                $or->status = $order['attributes']['status'] ?? null;
                $or->subtotal = $order['attributes']['subtotal'] ?? null;
                $or->subtotal_excl_discounts = $order['attributes']['subtotal_excl_discounts'] ?? null;
                $or->subtotal_incl_tax = $order['attributes']['subtotal_incl_tax'] ?? null;
                $or->total =$order['attributes']['total'] ?? null;
                $or->total_amount_refunded = $order['attributes']['total_amount_refunded'] ?? null;
                $or->total_discount =$order['attributes']['total_discount'] ?? null;
                // $or->deferred_item_count = $order['attributes']['status'];
                // $or->deferred_item_total =$order['attributes']['status'];
                // $or->deferred_item_total_incl_tax = $order['attributes']['status'];
                $or->total_tax = $order['attributes']['total_tax'] ?? null;
                // $or->billing_address = $order['relationships']['billing_address'];
                $or->broker_type = $order['relationships']['broker']['data']['type'] ?? null;
                $or->broker_id = $order['relationships']['broker']['data']['id'] ?? null;
                $or->cart_types = $order['relationships']['cart']['data']['type'] ?? null;
                $or->cart_id = $order['relationships']['cart']['data']['id'] ?? null;
                $or->fulfillment_partner_types =$order['relationships']['fulfillment_partner']['data']['type'] ?? null;
                $or->fulfillment_partner_id = $order['relationships']['fulfillment_partner']['data']['id'] ?? null;
                $or->user_types = $order['relationships']['user']['data']['type'] ?? null;
                $or->user_id = $order['relationships']['user']['data']['id'] ?? null;
                $or->order_lines_types = $order['relationships']['order_lines']['data'][0]['type'] ?? null;
                $or->order_lines_id = $order['relationships']['order_lines']['data'][0]['id'] ?? null;
                $or->originating_partner_types = $order['relationships']['originating_partner']['data']['type'] ?? null;
                $or->originating_partner_id = $order['relationships']['originating_partner']['data']['id'] ?? null;
                $or->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function saveUsersdata($users)
    {
        try {
            foreach($users as $user){

                $attributes = $user['attributes'];
            
                $or = new AllUsers();
                $or->type = $user['type'];
                $or->user_id = $user['id'];
                $or->email = $attributes['email'] ?? null;
                $or->first_name = $attributes['first_name'] ?? null;
                $or->last_name = $attributes['last_name'] ?? null;
                $or->birth_date = $attributes['birth_date'] ?? null;
                $or->phone_number = $attributes['phone_number'] ?? null;
                $or->address_line1 = $attributes['address_line1'] ?? null;
                $or->address_line2 = $attributes['address_line2'] ?? null;
                $or->address_line3 = $attributes['address_line3'] ?? null;
                $or->city = $attributes['city'] ?? null;
                $or->state_province = $attributes['state_province'] ?? null;
                $or->postal_code = $attributes['postal_code'] ?? null;
                $or->address_sorting_code = $attributes['address_sorting_code'] ?? null;
                $or->country = $attributes['country'] ?? null;
                $or->full_name = $attributes['full_name'] ?? null;
                $or->gender = $attributes['gender'] ?? null;
                $or->emergency_contact_name = $attributes['emergency_contact_name'] ?? null;
                $or->emergency_contact_relationship = $attributes['emergency_contact_relationship'] ?? null;
                $or->emergency_contact_phone = $attributes['emergency_contact_phone'] ?? null;
                $or->emergency_contact_email = $attributes['emergency_contact_email'] ?? null;
                $or->signed_waiver = $attributes['signed_waiver'] ?? null;
                $or->waiver_signed_datetime = $attributes['waiver_signed_datetime'] ?? null;
                $or->date_joined = $attributes['date_joined'] ?? null;
                $or->marketing_opt_in = $attributes['marketing_opt_in'] ?? null;
                $or->is_opted_in_to_sms = $attributes['is_opted_in_to_sms'] ?? null;
                $or->has_vip_tag_cache = $attributes['has_vip_tag_cache'] ?? null;
                $or->apply_account_balance_to_fees = $attributes['apply_account_balance_to_fees'] ?? null;
                $or->is_minimal = $attributes['is_minimal'] ?? null;
                $or->permissions = json_encode($attributes['permissions'] ?? []);
                $or->account_balance = $attributes['account_balance'] ?? null;
                $or->account_balances = json_encode($attributes['account_balances'] ?? []);
                $or->third_party_sync = $attributes['third_party_sync'] ?? null;
                $or->completed_class_count = $attributes['completed_class_count'] ?? null;
                $or->company_name = $attributes['company_name'] ?? null;
                $or->archived_at = $attributes['archived_at'] ?? null;
                $or->is_external_user = $attributes['is_external_user'] ?? null;
                $or->merged_into_id = $attributes['merged_into_id'] ?? null;
                $or->waivers = json_encode($attributes['waivers'] ?? []);
                $or->has_unsigned_waivers = $attributes['has_unsigned_waivers'] ?? null;
                $or->marketing_logs = json_encode($attributes['marketing_logs'] ?? []);
                $or->formatted_address = json_encode($attributes['formatted_address'] ?? []);
                $or->required_legal_documents = json_encode($attributes['required_legal_documents'] ?? []);
                $or->pronouns = $attributes['pronouns'] ?? null;
                $or->search_priority_category = $attributes['search_priority_category'] ?? null;
                $or->relationships = json_encode($user['relationships'] ?? []);
                $or->last_region = json_encode($user['relationships']['last_region']['data'] ?? []);
                $or->home_location_type = $user['relationships']['home_location']['data']['type'] ?? null;
                $or->home_location_id = $user['relationships']['home_location']['data']['id'] ?? null;
                $or->tags = json_encode($user['relationships']['tags']['data'] ?? []);
                $or->profile_image = json_encode($user['relationships']['profile_image']['data'] ?? []);

                $or->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function saveEmployeesdata($employee)
    {
        try {
            foreach($employee as $user){

                $attributes = $user['attributes'];
            
                $or = new Employees();
                $or->type = $user['type'];
                $or->employee_id = $user['id'];
                $or->payroll_id = $attributes['payroll_id'] ?? null;
                $or->is_active = $attributes['is_active'] ?? null;
                $or->can_chat = $attributes['can_chat'] ?? null;
                $or->is_beta_user = $attributes['is_beta_user'] ?? null;
                $or->relationships = json_encode($user['relationships'] ?? []);
                $or->user_type = $user['relationships']['user']['data']['type'] ?? null;
                $or->user_id = $user['relationships']['user']['data']['id'] ?? null;
                $or->recent_location_type = $user['relationships']['recent_location']['data']['type'] ?? null;
                $or->recent_location_id = $user['relationships']['recent_location']['data']['id'] ?? null;
                $or->public_profile_type = $user['relationships']['public_profile']['data']['type'] ?? null;
                $or->public_profile_id = $user['relationships']['public_profile']['data']['id'] ?? null;
                $or->groups = json_encode($user['relationships']['groups']['data'] ?? []);
                $or->turfs = json_encode($user['relationships']['turfs']['data'] ?? []);

                $or->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function saveMembership($orders)
    {
        try {
            foreach($orders as $order){

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
                $or->calculated_end_datetime = $order['attributes']['calculated_end_datetime'] ?? null;
                $or->calculated_start_datetime = $order['attributes']['calculated_start_datetime'] ?? null;
                $or->cancellation_datetime = $order['attributes']['cancellation_datetime'] ?? null;
                $or->cancellation_reason = $order['attributes']['cancellation_reason'] ?? null;
                $or->commitment_length = $order['attributes']['commitment_length'] ?? null;
                $or->end_date = $order['attributes']['end_date'] ?? null;
                $or->freeze_datetime = $order['attributes']['freeze_datetime'] ?? null;
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
                $or->purchase_date =$order['attributes']['purchase_date'];

                $or->remaining_renewal_count = $order['attributes']['remaining_renewal_count'];
                $or->renewal_count = $order['attributes']['renewal_count'] ?? null;
                $or->renewal_currency = $order['attributes']['renewal_currency'] ?? null;
                $or->renewal_limit = $order['attributes']['renewal_limit'] ?? null;
                $or->renewal_rate = $order['attributes']['renewal_rate'] ?? null;
                $or->renewal_rate_incl_tax = $order['attributes']['renewal_rate_incl_tax'] ?? null;

                $or->scheduled_end_datetime = $order['attributes']['scheduled_end_datetime'] ?? null;
                $or->should_display_price_include_tax = $order['attributes']['should_display_price_include_tax'] ?? null;
                $or->start_date = $order['attributes']['start_date'] ?? null;
                $or->status = $order['attributes']['status'] ?? null;
                $or->usage_interval_limit = $order['attributes']['usage_interval_limit'] ?? null;

                $or->current_membership_transaction_type = $order['relationships']['current_membership_transaction']['data']['type'] ?? null;
                $or->current_membership_transaction_id = $order['relationships']['current_membership_transaction']['data']['id'] ?? null;

                $or->membership_type = $order['relationships']['membership']['data']['type'] ?? null;
                $or->membership_type_id = $order['relationships']['membership']['data']['id'] ?? null;

                $or->membership_freeze_type = $order['relationships']['membership_freeze']['data']['type'] ?? null;
                $or->membership_freeze_id = $order['relationships']['membership_freeze']['data']['id'] ?? null;

                $or->events = json_encode($order['relationships']['events']['data']) ?? null;

                $or->membership_product_type = $order['relationships']['membership_product']['data']['id'] ?? null;
                $or->membership_product_id =$order['relationships']['membership_product']['data']['type'] ?? null;

                $or->purchase_location_type = $order['relationships']['purchase_location']['data']['id'] ?? null;
                $or->purchase_location_id = $order['relationships']['purchase_location']['data']['type'] ?? null;

                $or->user_type = $order['relationships']['user']['data']['id'] ?? null;
                $or->user_id = $order['relationships']['user']['data']['type'] ?? null;
              
                $or->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function saveFreezeMenberships($records)
    {
        try {
            foreach($records as $user){

                $attributes = $user['attributes'];
            
                $or = new FreezeMemberships();
                $or->type = $user['type'];
                $or->menbership_freeze_id = $user['id'];
                $or->freeze_datetime = $attributes['freeze_datetime'] ?? null;
                $or->reactivation_datetime = $attributes['reactivation_datetime'] ?? null;
                $or->relationships = json_encode($user['relationships'] ?? []);
                $or->membership_instance_type = $user['relationships']['membership_instance']['data']['type'] ?? null;
                $or->membership_instance_id = $user['relationships']['membership_instance']['data']['id'] ?? null;
                $or->broker_type = $user['relationships']['broker']['data']['type'] ?? null;
                $or->broker_id = $user['relationships']['broker']['data']['id'] ?? null;
                $or->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }

    public function savelocation($locations)
    {
        try {
            foreach($locations as $location){

                $or = new Locations();
                $or->type = $location['type'] ?? null;
                $or->location_id = $location['id'] ?? null;
                $or->name = $location['attributes']['name'] ?? null;
                $or->legal_entity = $location['attributes']['legal_entity'] ?? null;
                $or->timezone = $location['attributes']['timezone'] ?? null;
                $or->address_line1 = $location['attributes']['address_line1'] ?? null;
                $or->address_line2 = $location['attributes']['address_line2'] ?? null;
                $or->address_line3 = $location['attributes']['address_line3'] ?? null;
                $or->address_sorting_code = $location['attributes']['address_sorting_code'] ?? null;
                $or->city = $location['attributes']['city'] ?? null;
                $or->state_province = $location['attributes']['state_province'] ?? null;
                $or->postal_code = $location['attributes']['postal_code'] ?? null;
                $or->phone_number = $location['attributes']['phone_number'] ?? null;
                $or->email_address = $location['attributes']['email_address'] ?? null;
                $or->user_has_turf_access = $location['attributes']['user_has_turf_access'] ?? 0;
                $or->latitude = $location['attributes']['latitude'] ?? null;
                $or->longitude = $location['attributes']['longitude'] ?? null;
                $or->primary_language = $location['attributes']['primary_language'] ?? null;
                $or->listed = $location['attributes']['listed'] ?? 0;
                $or->currency_code = $location['attributes']['currency_code'] ?? null;
                $or->geo_check_in_distance = $location['attributes']['geo_check_in_distance'] ?? null;
                $or->gate_geo_check_in_by_distance = $location['attributes']['gate_geo_check_in_by_distance'] ?? null;
                $or->australian_business_number = $location['attributes']['australian_business_number'] ?? null;
                $or->vat_number = $location['attributes']['vat_number'] ?? null;
                $or->formatted_address = json_encode($location['attributes']['formatted_address'] ?? null);
                $or->use_tax_inclusive_pricing = $location['attributes']['use_tax_inclusive_pricing'] ?? null;


                $or->region_type = $location['relationships']['region']['data']['type'] ?? null;
                $or->region_id = $location['relationships']['region']['data']['id'] ?? null;

                $or->classrooms = json_encode($location['relationships']['classrooms']['data']) ?? null;

                $or->partner_type = $location['relationships']['partner']['data']['type'] ?? null;
                $or->partner_id = $location['relationships']['partner']['data']['id'] ?? null;

                $or->default_product_collection_type = $location['relationships']['default_product_collection']['data']['type'] ?? null;
                $or->default_product_collection_id = $location['relationships']['default_product_collection']['data']['id'] ?? null;

                $or->site_type = $location['relationships']['site']['data']['id'] ?? null;
                $or->site_id =$location['relationships']['site']['data']['type'] ?? null;

                $or->quick_sale_product_collection_type = $location['relationships']['quick_sale_product_collection']['data']['id'] ?? null;
                $or->quick_sale_product_collection_id = $location['relationships']['quick_sale_product_collection']['data']['type'] ?? null;

                $or->addons_product_collection_type = $location['relationships']['addons_product_collection']['data']['id'] ?? null;
                $or->addons_product_collection_id = $location['relationships']['addons_product_collection']['data']['type'] ?? null;
              
                $or->save();
            }
            return true;
        } catch(Exception $e) {
            dd($e);
            return false;
        }
       
    }
    


    public function testapibyid($api,$id)
    {
        $client = new Client();
        $url = env('API_URL').$api."/".$id;
        $accessToken = env('API_ACCESS_TOKEN');
        // $response = $client->request('GET', $url, [
        //     'headers' => [
        //         'Authorization' => 'Bearer ' . env('API_ACCESS_TOKEN'),
        //     ],
        //     'query' => [
        //         'location_id' => 48750
        //     ]
        // ]);
      
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . env('API_ACCESS_TOKEN'),
            ],
        ]);
      

        $statuscode = $response->getStatusCode();

        if($statuscode == 200){
            $body = $response->getBody()->getContents();
            return $body;
            
            // $data = json_decode($body,true);
            // echo "<pre>";
            // print_r($data);
            die();
        }
    }
}
