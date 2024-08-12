<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\MembershipInstances;
use Illuminate\Support\Facades\DB;
use App\Models\Locations;

class AdminDashboardController extends Controller
{
    public function index(){
        return view('admin_dashboard.index');
    }

    public function locations(){
        $locations = Locations::all();
        return view('admin_dashboard.locations.location',compact('locations'));
    }

    public function getLocation(Request $request){
        $client = new Client();
        $url = "https://kbxf.marianatek.com/api/locations";
        $response = $client->request('GET',$url);
        $statuscode = $response->getStatusCode();

        if($statuscode == 200){
            $body = $response->getBody()->getContents();
            $data = json_decode($body,true);

            return response()->json($data);
        }
    }

    // public function memberships(){
    //     $csvFileName = 'memberships.csv';
    //     $csvFilePath = public_path('files/' . $csvFileName);
    //     $fileExists = file_exists($csvFilePath);

    //     $memberships = [];
    //     $membership_name = [];
    //     $total = 0;
    //     if (($handle = fopen($csvFilePath, 'r')) !== false) {
    //         $headers = fgetcsv($handle);

    //         while (($row = fgetcsv($handle)) !== false) {
    //             if(count($headers) === count($row)){
    //                 $membership = array_combine($headers, $row);
    //                 $membership_name[] = $membership['attributes.membership_name'];
    //                 $memberships[] = $membership;
    //             }
                
    //         }
    //         fclose($handle);
    //     }

    //     $membershipCollection = collect($memberships);

    //     $club_key = 'Club';
    //     $clubPackageCount = $membershipCollection->filter(function ($membership) use ($club_key) {
    //         if (strpos($membership['attributes.membership_name'], $club_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $starter_key = 'Starter';
    //     $starterPackageCount = $membershipCollection->filter(function ($membership) use ($starter_key) {
    //         if (strpos($membership['attributes.membership_name'], $starter_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $gold_key = 'Gold';
    //     $goldPackageCount = $membershipCollection->filter(function ($membership) use ($gold_key) {
    //         if (strpos($membership['attributes.membership_name'], $gold_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $vip_key = 'VIP';
    //     $vipPackageCount = $membershipCollection->filter(function ($membership) use ($vip_key) {
    //         if (strpos($membership['attributes.membership_name'], $vip_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $two_week_unlimited_key = '2 Week Unlimited Pass';
    //     $twoWeekPackageCount = $membershipCollection->filter(function ($membership) use ($two_week_unlimited_key) {
    //         if (strpos($membership['attributes.membership_name'], $two_week_unlimited_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $one_week_free_key = '1 Week Free Trial';
    //     $oneWeekFreePackageCount = $membershipCollection->filter(function ($membership) use ($one_week_free_key) {
    //         if (strpos($membership['attributes.membership_name'], $one_week_free_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $one_week_unlimited_key = '1 Week Unlimited Pass (Kids)';
    //     $oneWeekUnlimitedPackageCount = $membershipCollection->filter(function ($membership) use ($one_week_unlimited_key) {
    //         if (strpos($membership['attributes.membership_name'], $one_week_unlimited_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();

    //     $one_week_trial_key = '1 Week Trial for $10';
    //     $oneWeekFreeTrialPackageCount = $membershipCollection->filter(function ($membership) use ($one_week_trial_key) {
    //         if (strpos($membership['attributes.membership_name'], $one_week_trial_key) !== false) {
    //             return true; 
    //         }
    //         return false; 
    //     })->count();
       

    //     $total = $clubPackageCount + $starterPackageCount + $goldPackageCount + $vipPackageCount +  $twoWeekPackageCount + $oneWeekFreePackageCount + $oneWeekUnlimitedPackageCount + $oneWeekFreeTrialPackageCount; 
    //     // $currentPage = LengthAwarePaginator::resolveCurrentPage();
    
    //     // $perPage = 20;
    
    //     // $currentPageMembers = $membershipCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
    
    //     // $paginatedMembers = new LengthAwarePaginator($currentPageMembers, $membershipCollection->count(), $perPage, $currentPage, [
    //     //     'path' => LengthAwarePaginator::resolveCurrentPath(),
    //     // ]);

    //     return view('admin_dashboard.memberships.index',compact('total','clubPackageCount','starterPackageCount','goldPackageCount','vipPackageCount','twoWeekPackageCount','oneWeekFreePackageCount','oneWeekUnlimitedPackageCount','oneWeekFreeTrialPackageCount'));
    // }

    // public function memberships(){
        // $instance = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['status','active']])->get();
        // dd(count($instance));
        // $locations = Locations::all();
        // $starter = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Starter%')
        //                         ->where('status','active')
        //                         ->groupBy('membership_name')
        //                         ->get();
        // $club = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Club%')
        //                         ->where('status','active')
        //                         ->groupBy('membership_name')
        //                         ->get();
        
        // $gold = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Gold%')
        //                         ->where('status','active')
        //                         ->groupBy('membership_name')
        //                         ->get();
        // $vip = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%VIP%')
        //                         ->where('status','active')
        //                         ->groupBy('membership_name')
        //                         ->get();
        // $pending_starter = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Starter%')
        //                         ->where('status','pending_start_date')
        //                         ->orWhere('status','pending_customer_activation')
        //                         ->groupBy('membership_name')
        //                         ->get();
                   
        // $pending_club = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Club%')
        //                         ->where('status','pending_start_date')
        //                         ->orWhere('status','pending_customer_activation')
        //                         ->groupBy('membership_name')
        //                         ->get();   
                                
        // $pending_gold = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Gold%')
        //                         ->where('status','pending_start_date')
        //                         ->orWhere('status','pending_customer_activation')
        //                         ->groupBy('membership_name')
        //                         ->get();   
        
        // $pending_vip = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%VIP%')
        //                         ->where('status','pending_start_date')
        //                         ->orWhere('status','pending_customer_activation')
        //                         ->groupBy('membership_name')
        //                         ->get();     
                                
        // $cancel_starter = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Starter%')
        //                         ->where('status','cancelled')
        //                         ->orWhere('status','terminated')
        //                         ->groupBy('membership_name')
        //                         ->get();   

        // $cancel_club = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Club%')
        //                         ->where('status','cancelled')
        //                         ->orWhere('status','terminated')
        //                         ->groupBy('membership_name')
        //                         ->get(); 

        // $cancel_gold = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Gold%')
        //                         ->where('status','cancelled')
        //                         ->orWhere('status','terminated')
        //                         ->groupBy('membership_name')
        //                         ->get();    

        // $cancel_vip = DB::table('membership_instances')
        //                         ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
        //                         ->where('membership_name','LIKE','%Vip%')
        //                         ->where('status','cancelled')
        //                         ->orWhere('status','terminated')
        //                         ->groupBy('membership_name')
        //                         ->get();

        // $total = count($starter)+count($club)+count($gold)+count($vip);
        // $pending_total = count($pending_starter)+count($pending_club)+count($pending_gold)+count($pending_vip);
        // $cancel_total = count($cancel_starter)+count($cancel_club)+count($cancel_gold)+count($cancel_vip);

        // return view('admin_dashboard.memberships.index',compact('starter','club','gold','vip','pending_starter','pending_club','pending_gold','pending_vip','total','pending_total','locations','cancel_starter','cancel_club','cancel_gold','cancel_vip','cancel_total'));
    // }

    public function memberships(){
        $locations = Locations::all();

        $starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['status','active']])->get();
        $club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['status','active']])->get();
        $gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['status','active']])->get();
        $vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['status','active']])->get();

        $pending_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        $pending_club = MembershipInstances::where([['membership_name','LIKE','%Club%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        $pending_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        $pending_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();

        $cancel_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%']])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        $cancel_club = MembershipInstances::where([['membership_name','LIKE','%Club%']])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        $cancel_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%']])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        $cancel_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%']])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        
        $total = count($starter)+count($club)+count($gold)+count($vip);
        $pending_total = count($pending_starter)+count($pending_club)+count($pending_gold)+count($pending_vip);
        $cancel_total = count($cancel_starter)+count($cancel_club)+count($cancel_gold)+count($cancel_vip);

        return view('admin_dashboard.memberships.index',compact('locations','starter','club','gold','vip','pending_starter','pending_club','pending_gold','pending_vip','cancel_starter','cancel_club','cancel_gold','cancel_vip','total','pending_total','cancel_total'));
    }

    public function getMembershipByLocation(Request $request){
        $starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['status','active'],['purchase_location_id',$request->id]])->get();
        $club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['status','active'],['purchase_location_id',$request->id]])->get();
        $gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['status','active'],['purchase_location_id',$request->id]])->get();
        $vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['status','active'],['purchase_location_id',$request->id]])->get();

        $pending_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['purchase_location_id',$request->id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        $pending_club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['purchase_location_id',$request->id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        $pending_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['purchase_location_id',$request->id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        $pending_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['purchase_location_id',$request->id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->get();
        
        $cancel_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        $cancel_club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        $cancel_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
        $cancel_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure'])->get();
    
        $total = count($starter)+count($club)+count($gold)+count($vip);
        $pending_total = count($pending_starter)+count($pending_club)+count($pending_gold)+count($pending_vip);
        $cancel_total = count($cancel_starter)+count($cancel_club)+count($cancel_gold)+count($cancel_vip);

        $active_data = array($starter,$club,$gold,$vip,$total);
        $pending_data = array($pending_starter,$pending_club,$pending_gold,$pending_vip,$pending_total);
        $cancel_data = array($cancel_starter,$cancel_club,$cancel_gold,$cancel_vip,$cancel_total);

        $response = [
            'active' => $active_data,
            'pending' => $pending_data,
            'cancel' => $cancel_data,
            'status' => 200
        ];

        return response()->json($response);
    }

    // public function getMembershipByLocation(Request $request){
    //     // $membership_instance = MembershipInstances::where('purchase_location_id',$request->id)->with('locations');
    //     // return $membership_instance;
    //     $starter = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Starter%')
    //                             ->where('status','active')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();

    //     $club = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Club%')
    //                             ->where('status','active')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();
        
    //     $gold = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Gold%')
    //                             ->where('status','active')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();
    //     $vip = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%VIP%')
    //                             ->where('status','active')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();
    //     $pending_starter = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Starter%')
    //                             ->where('status','pending_start_date')
    //                             ->orWhere('status','pending_customer_activation')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();
                   
    //     $pending_club = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Club%')
    //                             ->where('status','pending_start_date')
    //                             ->orWhere('status','pending_customer_activation')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();   
                                
    //     $pending_gold = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Gold%')
    //                             ->where('status','pending_start_date')
    //                             ->orWhere('status','pending_customer_activation')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();   
        
    //     $pending_vip = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%VIP%')
    //                             ->where('status','pending_start_date')
    //                             ->orWhere('status','pending_customer_activation')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();     

    //     $cancel_starter = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Starter%')
    //                             ->where('status','cancelled')
    //                             ->orWhere('status','terminated')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();   

    //     $cancel_club = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Club%')
    //                             ->where('status','cancelled')
    //                             ->orWhere('status','terminated')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get(); 

    //     $cancel_gold = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Gold%')
    //                             ->where('status','cancelled')
    //                             ->orWhere('status','terminated')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();    

    //     $cancel_vip = DB::table('membership_instances')
    //                             ->select('membership_name',DB::raw('SUM(renewal_rate) as total_amount'),DB::raw('count(*) as total_count'))
    //                             ->where('membership_name','LIKE','%Vip%')
    //                             ->where('status','cancelled')
    //                             ->orWhere('status','terminated')
    //                             ->where('purchase_location_id',$request->id)
    //                             ->groupBy('membership_name')
    //                             ->get();


    //     $total = count($starter)+count($club)+count($gold)+count($vip);
    //     $pending_total = count($pending_starter)+count($pending_club)+count($pending_gold)+count($pending_vip);
    //     $cancel_total = count($cancel_starter)+count($cancel_club)+count($cancel_gold)+count($cancel_vip);

    //     $active_data = array($starter,$club,$gold,$vip,$total);
    //     $pending_data = array($pending_starter,$pending_club,$pending_gold,$pending_vip,$pending_total);
    //     $cancel_data = array($cancel_starter,$cancel_club,$cancel_gold,$cancel_vip,$cancel_total);

    //     $response = [
    //         'active' => $active_data,
    //         'pending' => $pending_data,
    //         'cancel' => $cancel_data,
    //         'status' => 200
    //     ];

    //     return response()->json($response);
    // }

    public function getUserByMemberships(Request $request){
        $status = $request->status;
        $location = $request->location;
        $membership_name = $request->membership;
     
        $query = MembershipInstances::query();
        
        $query->where('membership_name', 'LIKE', "%$membership_name%");

        if($status === 'cancelled'){
            $query->whereIn('status',[$status,'terminated','payment_failure']);
        }elseif($status === 'pending'){
            $query->whereIn('status', ['pending_start_date','pending_customer_activation']);
        }else{
            $query->where('status', $status);
        }

        if($location !== null) {
            $query->where('purchase_location_id', $location);
        }

        $query->with('user');
        $memberships = $query->get();

        return view('admin_dashboard.memberships.detail_page',compact('memberships'));
    }

}

