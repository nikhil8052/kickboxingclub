<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTransaction;
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

class MembershipController extends Controller
{
    // Dump Into the Db 
    public function dumpToDatabase(){     
        // $client = new Client();
        // // $url="https://kbxf.marianatek.com/api/membership_transactions";
        // // $url = "https://kbxf.marianatek.com/api/memberships";
        // // $url = "https://kbxf.marianatek.com/api/credits";
        // // $url = "https://kbxf.marianatek.com/api/time_clock_shifts";

        // // $url = "https://kbxf.marianatek.com/api/employees";

        // $url = "https://kbxf.marianatek.com/api/users";
        // $bearerToken = env('API_ACCESS_TOKEN');

        // $currentPage = 1;
        // $pageSize = 500;
        // $hasMorePages = true;

        // while ($hasMorePages) {
        //     $response = $client->request('GET', $url, [
        //         'headers' => [
        //             'Authorization' => 'Bearer ' . $bearerToken,
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

        //         // $savedMemberships = $this->saveMemberships($data['data']);
        //         // $saveTimeShifts = $this->saveTimeClockShift($data['data']);

        //         // $saveCredits = $this->saveCredits($data['data']);
        //         // dd($data['data']);

        //         // $saveTransaction = $this->saveMembershipsTransaction($data['data']);

        //         $saveUsers = $this->saveUsersdata($data['data']);
        //         if($saveUsers) {
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

        // return response()->json(['Memberships saved into database']);
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
    
        if ($location != null) {
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
        $allinstances = MembershipInstances::where('type','membership_instances')->with('user.location')->get();

        return view('admin_dashboard.memberships_instances.index',compact('locations','allinstances'));
    }

    public function GetInstances(Request $request) {
        $query = MembershipInstances::query();
        $query = $query->where('type', 'membership_instances');
    
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
    
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
    
            $query->whereBetween('purchase_date', [$start, $end]);
        }
    
        if ($location != null) {
            $query->whereHas('user.location', function ($q) use ($location) {
                $q->where('location_id', $location);
            });
        }
    
        $query->with('user.location');
    
        $membershipInstance = $query->get();
    
        return response()->json([
            'data' => $membershipInstance
        ]);
    }
    

}
