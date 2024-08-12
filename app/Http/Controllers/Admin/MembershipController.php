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

class MembershipController extends Controller
{
    // Dump Into the Db 
    public function dumpToDatabase(){     
        $client = new Client();
        // $url="https://kbxf.marianatek.com/api/membership_transactions";
        // $url = "https://kbxf.marianatek.com/api/memberships";
        $url = "https://kbxf.marianatek.com/api/credits";
        // $url = "https://kbxf.marianatek.com/api/time_clock_shifts";
        $bearerToken = env('API_ACCESS_TOKEN');

        $currentPage = 1;
        $pageSize = 9;
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

                $saveCredits = $this->saveCredits($data['data']);
                // dd($data['data']);
                if($saveCredits) {
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
        return view('admin_dashboard.membership_transaction.index');
    }

    public function getMembershipsTransaction(){
        $membershipTransaction = MembershipTransaction::where('type','membership_transactions')
                                ->select('membership_name',DB::raw('count(*) as total_count'))
                                ->groupBy('membership_name')
                                ->get();

        return response()->json($membershipTransaction);
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

    public function getBillingStats(Request $request,$month){

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

    public function getMembershipTransactionByDate(Request $request){
        return $request->all();
    }



}
