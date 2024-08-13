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

        $cancel_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        $cancel_club = MembershipInstances::where([['membership_name','LIKE','%Club%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        $cancel_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        $cancel_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        
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
        
        $cancel_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        $cancel_club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        $cancel_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
        $cancel_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['purchase_location_id',$request->id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->get();
    
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

    public function getUserByMemberships(Request $request){
        $status = $request->status;
        $location = $request->location;
        $membership_name = $request->membership;

        $start = $request->start;
        $end = $request->end;
     
        $query = MembershipInstances::query();
        
        $query->where('membership_name', 'LIKE', "%$membership_name%");

        if($status === 'cancelled'){
            $query->whereIn('status',[$status,'terminated','payment_failure','ding_failure']);
        }elseif($status === 'pending'){
            $query->whereIn('status', ['pending_start_date','pending_customer_activation']);
        }else{
            $query->where('status', $status);
        }

        if($location !== null) {
            $query->where('purchase_location_id', $location);
        }

        // if($start !== null && $end !== null){
        //     $startDate = Carbon::parse($start);
        //     $endDate = Carbon::parse($end);

        //     $query->whereBetween('purchase_date', [$startDate, $endDate]);
        // }

        $query->with('user');
        $memberships = $query->get();

        return view('admin_dashboard.memberships.detail_page',compact('memberships'));
    }

    public function getMembershipByDate(Request $request){
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);
        $location_id = $request->location_id;

        if($startDate && $endDate){
            $starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['status','active']])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['status','active']])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['status','active']])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['status','active']])->whereBetween('purchase_date', [$startDate, $endDate])->get();

            $pending_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $pending_club = MembershipInstances::where([['membership_name','LIKE','%Club%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $pending_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $pending_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%']])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();

            $cancel_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $cancel_club = MembershipInstances::where([['membership_name','LIKE','%Club%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $cancel_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
            $cancel_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%']])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
        
            if($location_id !== null){
                $starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['status','active'],['purchase_location_id',$location_id]])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['status','active'],['purchase_location_id',$location_id]])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['status','active'],['purchase_location_id',$location_id]])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['status','active'],['purchase_location_id',$location_id]])->whereBetween('purchase_date', [$startDate, $endDate])->get();

                $pending_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['purchase_location_id',$location_id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $pending_club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['purchase_location_id',$location_id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $pending_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['purchase_location_id',$location_id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $pending_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['purchase_location_id',$location_id]])->whereIn('status',['pending_customer_activation','pending_start_date'])->whereBetween('purchase_date', [$startDate, $endDate])->get();

                $cancel_starter = MembershipInstances::where([['membership_name','LIKE','%Starter%'],['purchase_location_id',$location_id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $cancel_club = MembershipInstances::where([['membership_name','LIKE','%Club%'],['purchase_location_id',$location_id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $cancel_gold = MembershipInstances::where([['membership_name','LIKE','%Gold%'],['purchase_location_id',$location_id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();
                $cancel_vip = MembershipInstances::where([['membership_name','LIKE','%Vip%'],['purchase_location_id',$location_id]])->whereIn('status',['cancelled','terminated','payment_failure','ding_failure'])->whereBetween('purchase_date', [$startDate, $endDate])->get();            
            }
        }

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

}

