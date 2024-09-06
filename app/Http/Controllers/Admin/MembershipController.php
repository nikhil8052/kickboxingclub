<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTransaction;
use App\Models\ActiveMember;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Models\Membership;
use App\Models\MembershipLocation;
use App\Models\MembershipInstances;
use App\Models\TimeClockShift;
use App\Models\Credit;
use App\Models\Locations;
use App\Models\EmployeePayRate;
use App\Models\AllUsers;
use App\Models\BillingCycle;
use App\Models\Orders;
use App\Models\MembershipTrial;
use App\Models\Group;
use App\Models\EmployeeGroup;
use App\Models\Employees;
use Cache;


class MembershipController extends Controller
{
    // Dump Into the Db 
    public function dumpToDatabase(){     

        $client = new Client();
        // $url="https://kbxf.marianatek.com/api/membership_transactions";
        // $url = "https://kbxf.marianatek.com/api/memberships";
        // $url = "https://kbxf.marianatek.com/api/credits";
        // $url = "https://kbxf.marianatek.com/api/time_clock_shifts";

        $url = "https://kbxf.marianatek.com/api/employees";

        // $url = "https://kbxf.marianatek.com/api/users";
        // $url = "https://kbxf.marianatek.com/api/membership_instances";

        // $url = "https://kbxf.marianatek.com/api/groups";
        $bearerToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
        $pageSize = 100;
        $hasMorePages = true;

        while ($hasMorePages) {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $bearerToken,
                ],
                'query' => [
                    'page' => $currentPage,
                    'page_size' => $pageSize,
                ],
            ]);

            $statuscode = $response->getStatusCode();

            if ($statuscode == 200) {
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                // $savedMemberships = $this->saveMemberships($data['data']);
                // $saveTimeShifts = $this->saveTimeClockShift($data['data']);

                // $saveCredits = $this->saveCredits($data['data']);
                // dd($data['data']);

                // $saveTransaction = $this->saveMembershipsTransaction($data['data']);

                // $saveUsers = $this->saveUsersdata($data['data']);
                // $saveMembershipsBilling = $this->saveBillingCycle($data['data']);

                // $saveInstance = $this->saveMembership($data['data']);

                // $saveGroups = $this->saveGroups($data['data']);
                $saveEmployeewithGroup = $this->saveEmployeeGroups($data['data']);

                if($saveEmployeewithGroup) {
                    $totalPages = $data['meta']['pagination']['pages'] ?? 0;
                    if ($currentPage >= $totalPages) {
                        $hasMorePages = false;
                    } else {
                        $hasMorePages = false;
                        $currentPage++;
                    }
                } else {
                    
                    die();
                }
                
            } else {
                $hasMorePages = false;
            }
        } 

        return response()->json(['Memberships saved into database']);
    }

    public function saveEmployeeGroups($employeeGroups){
        try{
            foreach($employeeGroups as $data){
                $employee_id = $data['id'];
                $attributes = $data['attributes'] ?? [];
                $relationships = $data['relationships'] ?? [];

                $employee = new Employees;
                $employee->type = $data['type'];
                $employee->employee_id = $employee_id;
                $employee->payroll_id = $attributes['payroll_id'] ?? null;
                $employee->is_active = $attributes['is_active'] ?? null;
                $employee->can_chat = $attributes['can_chat'] ?? null;
                $employee->is_beta_user = $attributes['is_beta_user'] ?? null;
                $employee->relationships = json_encode($relationships) ?? null;
                $employee->user_type = $relationships['user']['data']['type'] ?? null;
                $employee->user_id = $relationships['user']['data']['id'] ?? null;
                $employee->recent_location_type = $relationships['recent_location']['data']['type'] ?? null;
                $employee->recent_location_id = $relationships['recent_location']['data']['id'] ?? null;
                $employee->public_profile_type = $relationships['public_profile']['data']['type'] ?? null;
                $employee->public_profile_id = $relationships['public_profile']['data']['id'] ?? null;
                $employee->groups = json_encode($relationships['groups']['data']) ?? null;
                $employee->turfs = json_encode($relationships['turfs']['data']) ?? null;
                $employee->save();

                $groups = $relationships['groups']['data'];

                foreach($groups as $group){
                    $employeeGroup = new EmployeeGroup;
                    $employeeGroup->employee_id = $employee_id;
                    $employeeGroup->group_id = $group['id'];
                    $employeeGroup->save();
                }
            }
        }catch(Exception $e) {
            return false;
        }
    }

    public function saveGroups($groups){
        try{
            foreach($groups as $data){
                $attributes = $data['attributes'] ?? [];
                // $permissions = json_encode($attributes['permissions']);
                $relationships = $data['relationships'] ?? [];

                $group = new Group;
                $group->type = $data['type'] ?? null;
                $group->group_id = $data['id'] ?? null;
                $group->group_name = $attributes['name'] ?? null;
                $group->description = $attributes['description'] ?? null;
                if(isset($attributes['permissions']) && is_array($attributes['permissions'])) {
                    $group->permissions = json_encode($attributes['permissions']);
                } else {
                    $group->permissions = null; 
                }
                $group->user_can_assign_group = $attributes['user_can_assign_group'] ?? null;
                $group->public = $attributes['public'] ?? null;
                $group->relationships = json_encode($relationships) ?? null;
                $group->save();
            }
        }catch(Exception $e) {
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
            return true;
        } catch(Exception $e) {
            return false;
        }
       
    }

    public function saveBillingCycle($billings){
        try{    
            foreach($billings as $billing){
                $membership_instance_id = $billing['id'];
                $renewal_rate = $billing['attributes']['renewal_rate'];
                $billing_cycles = $billing['attributes']['billing_cycles'];
                $location_id = $billing['relationships']['purchase_location']['data']['id'] ?? null;
                $status = $billing['attributes']['status'];

                foreach($billing_cycles as $bill){
                    $billingCycle = new BillingCycle;
                    $billingCycle->billing_id = $bill['id'];
                    $billingCycle->membership_instance_id = $membership_instance_id;
                    $billingCycle->start_datetime = $bill['start_datetime'] == null 
                        ? null 
                        : Carbon::parse($bill['start_datetime']); 

                    $startDatetime = $billingCycle->start_datetime == null 
                        ? null 
                        : $billingCycle->start_datetime->setTimezone('America/Los_Angeles'); 

                    $billingCycle->start_date_copy = $startDatetime == null 
                        ? null 
                        : $startDatetime->format('Y-m-d');

                    $billingCycle->end_datetime = $bill['end_datetime'] == null 
                        ? null 
                        : Carbon::parse($bill['end_datetime']); 

                    $endDatetime = $billingCycle->end_datetime == null 
                        ? null 
                        : $billingCycle->end_datetime->setTimezone('America/Los_Angeles'); 

                    $billingCycle->end_date_copy = $endDatetime == null 
                        ? null 
                        : $endDatetime->format('Y-m-d');
                   
                    $billingCycle->renewal_rate = $renewal_rate;
                    $billingCycle->location_id = $location_id;
                    $billingCycle->status = $status;
                    $billingCycle->save();
                }
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function saveUsersdata($users)
    {
        try {
            foreach($users as $user){

                $attributes = $user['attributes'];
                $dateJoined = $attributes['date_joined'] ?? null;
                $join_date = Carbon::parse($dateJoined)->format('Y-m-d');

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
                $or->date_joined = $join_date;
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

    public function saveEmployeeID($employees){
        try{
            foreach($employees as $emp){
                $employee_pay_rate = new EmployeePayRate;
                $employee_pay_rate->employee_id = $emp['id'];
                $employee_pay_rate->save();
            }
        }catch(Exception $e){
            return response()->json([$e->getMessage()]);
        }
    }

    public function saveMembershipsTransaction($membership_transaction){
        try {
            foreach($membership_transaction as $membership){
                $attributes = $membership['attributes'] ?? [];
                $relationships = $membership['relationships'] ?? [];

                $membership_transaction = new MembershipTransaction;
                $membership_transaction->type = $membership['type'];
                $membership_transaction->membership_name = $membership['attributes']['membership_name'];
                $membership_transaction->membership_transactions_id = $membership['id'];
                $membership_transaction->transaction_amount = $membership['attributes']['transaction_amount'];
                $membership_transaction->user_id = $membership['relationships']['user']['data']['id'];
                $membership_transaction->membership_instances_id = $membership['relationships']['membership_instance']['data']['id'];
                $membership_transaction->transaction_datetime = Carbon::parse($membership['attributes']['transaction_datetime']);
                $membership_transaction->transaction_date = Carbon::parse($membership['attributes']['transaction_datetime'])->format('Y-m-d');
                $membership_transaction->save();
            }
            return true;
        } catch(Exception $e) {
            return response()->json([$e->getMessage()]);
        }
       
    }

    public function saveMemberships($memberships){
        try {
            $locations = [];
            $membership_ids = [];
            foreach($memberships as $data){            
                $attributes = $data['attributes'] ?? [];
                $relationships = $data['relationships'] ?? [];
                $locations = $relationships['locations']['data'];
                $membership_id = $data['id'];
            
                $membership = new Membership;
                $membership->membership_id = $membership_id;
                $membership->name = $attributes['name'];
                $membership->type = $data['type'];
                $membership->is_active = $attributes['is_active'];
                $membership->save();

                foreach($locations as $location){
                    $membership_location = new MembershipLocation;
                    $membership_location->membership_id = $membership_id;
                    $membership_location->location_id = $location['id'];
                    $membership_location->save();               
                }

            }
            return true;

        } catch(Exception $e) {
            return response()->json([$e->getMessage()]);
        }
       
    }

    public function saveTimeClockShift($shifts){
        try {
            foreach($shifts as $data){            
                $attributes = $data['attributes'] ?? [];
                $relationships = $data['relationships'] ?? [];
            
                $time_clock = new TimeClockShift;
                $time_clock->type = $data['type'];
                $time_clock->time_clock_id = $data['id'];
                $time_clock->start_datetime = $attributes['start_datetime'];
                $time_clock->end_datetime = $attributes['end_datetime'];
                $time_clock->duration = $attributes['duration'];
                $time_clock->user_has_turf_access = $attributes['user_has_turf_access'];
                $time_clock->relationships = json_encode($relationships);
                $time_clock->employee_id = $relationships['employee']['data']['id'];
                $time_clock->location_id = $relationships['location']['data']['id'];
                $time_clock->save(); 

            }
            return true;

        } catch(Exception $e) {
            return response()->json([$e->getMessage()]);
        }
    }

    public function saveCredits($creditss){
        try {
            foreach($creditss as $data){            
                $attributes = $data['attributes'] ?? [];
                $relationships = $data['relationships'] ?? [];
                
                // dd(json_encode($data['attributes']['currency_codes']));

                $credits = new Credit;
                $credits->type = $data['type'];
                $credits->credit_id = $data['id'];
                $credits->name = $data['attributes']['name'];
                $credits->guest_usage = $data['attributes']['guest_usage'];
                $credits->is_active = $data['attributes']['is_active'];
                $credits->location_availability_override = $data['attributes']['location_availability_override'];
                $credits->user_has_any_locations = $data['attributes']['user_has_any_locations'];
                $credits->user_has_all_locations = $data['attributes']['user_has_all_locations'];
                $credits->currency_codes = json_encode($data['attributes']['currency_codes']);
                $credits->is_live_stream = $data['attributes']['is_live_stream'];
                $credits->ding_exempt = $data['attributes']['ding_exempt'];
                $credits->relationships = json_encode($relationships);
                $credits->credit_slots_id = json_encode($relationships['credit_slots']['data']);
                $credits->booking_windows_id = json_encode($relationships['booking_window']['data']);
                $credits->late_cancel_windows_id = json_encode($relationships['late_cancel_window']['data']);
                $credits->locations_id = json_encode($relationships['locations']['data']);
                $credits->class_session_tags_id = json_encode($relationships['class_tag_rules']['data']);
                $credits->save();

            }
            return true;

        } catch(Exception $e) {
            return response()->json([$e->getMessage()]);
        }
    }

    public function MembershipsTransaction(){
        $locations = Locations::all();
        $membershipTransaction = MembershipTransaction::where('type','membership_transactions')
                                ->select('membership_name',DB::raw('count(*) as total_count'))
                                ->groupBy('membership_name')
                                ->get();
                        
        return view('admin_dashboard.membership_transaction.index',compact('locations','membershipTransaction'));
    }

    public function getMembershipsTransaction(Request $request) {
        $query = MembershipTransaction::query();
    
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
    
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $query->whereBetween('transaction_date', [$start, $end]);
        }

        if ($location) {
            $query->whereHas('membership_instance', function ($query) use ($location) {
                $query->where('purchase_location_id', $location);
            });
        }

        $query->select('membership_name', DB::raw('count(membership_name) as total_count'))
            ->groupBy('membership_name');
    
        $membershipTransaction = $query->get();
    
        return response()->json([
            'data' => $membershipTransaction
        ]);
    }
    

    public function BillingStats(){ 
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        $dates = [];

        for($day = 1; $day <= $daysInMonth; $day++){
            $dates[] = Carbon::createFromDate($year, $month, $day);
            
        }

        return view('admin_dashboard.billing_stats.index',compact('dates'));
    }

    // public function getBillingStats(Request $request){
    //     $month = $request->month ?? Carbon::now()->format('Y-m');
    //     $date = Carbon::createFromFormat('Y-m', $month);
    //     $year = $date->year;
    //     $month = $date->month;
    
    //     $cacheKey = "billing_stats_{$year}_{$month}";
        
    //     $dates = Cache::remember($cacheKey, 60 * 60, function () use ($year, $month) {
    //         $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
    //         return collect(range(1, $daysInMonth))->map(function ($day) use ($year, $month) {
    //             return Carbon::createFromDate($year, $month, $day);
    //         });
    //     });
    
    //     return view('admin_dashboard.billing_stats.index', compact('dates'));
    // }
    
    public function getBillingStats(Request $request){
        $month = $request->month;
        $date = Carbon::createFromFormat('Y-m', $month);
        $month = $date->format('m');
        $monthInt = $date->month;

        $year = $date->format('Y');
        $yearInt = $date->year;

        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        $dates = [];
        for($day=1; $day <= $daysInMonth; $day++){
            $dates[] = Carbon::createFromDate($year, $month, $day);
        }
    
        return view('admin_dashboard.billing_stats.index',compact('dates'));
    }


    public function Instances(){
        $locations = Locations::all();
        // $allinstances = MembershipInstances::where('type','membership_instances')->with('user.location')->get();
        
        $allinstances = null;

        return view('admin_dashboard.memberships_instances.index',compact('locations','allinstances'));
    }

    public function GetInstances(Request $request) {
        $query = MembershipInstances::query();
    
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
    
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
    
            $query->whereBetween('purchase_date_copy', [$start, $end]);
        }
    
        if ($location != null) {
            $query->whereHas('user.location', function ($q) use ($location) {
                $q->where('location_id', $location);
            });
            //  $query->whereBetween('purchase_date_copy', [$start, $end]);
        }
    
        $membershipexclud = ActiveMember::all();
        $membershipsExcludes = [];
        foreach($membershipexclud as $exclude) {
            $membershipsExcludes[] = $exclude->name;
        }

    
        foreach ($membershipsExcludes as $excludeName) {
            $query->where('membership_name', 'NOT LIKE', "%$excludeName%");
        }
    
        $membershipInstance = $query->with(['user','locations','membership'])->get();
    
        return response()->json([
            'data' => $membershipInstance
        ]);
    }
}
