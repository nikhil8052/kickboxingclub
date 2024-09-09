<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTrial;
use App\Models\MembershipInstances;
use Illuminate\Support\Facades\DB;
use App\Models\TimeClockShift;
use App\Models\ActiveMember;
use App\Models\AllUsers;
use App\Models\Locations;
use Carbon\Carbon;

class LeadSectionController extends Controller
{
    public function index(){
        $locations = Locations::all();
        return view('admin_dashboard.leads.index',compact('locations'));
    }

    public function getTrialsPurchased(Request $request){
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
        $membership_instance = MembershipInstances::query();

        if($startDate && $endDate){
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
    
            $membership_instance->whereBetween('purchase_date_copy', [$start, $end]);
        }

        if($location != null){
            $membership_instance->whereHas('user.location', function ($q) use ($location) {
                $q->where('location_id', $location);
            });
        }

        $membershipstrail = MembershipTrial::where('status',1)->get();

        $membershipstrailnames = [];
        foreach($membershipstrail as $trial) {
            $membershipstrailnames[] = $trial->name;
        }

        $membership_instance->where(function ($query) use ($membershipstrailnames) {
            foreach ($membershipstrailnames as $trialName) {
                $query->orWhere('membership_name', 'LIKE', "%$trialName%");
            }
        });

        $membership_instance->where('status','pending_customer_activation');
        $alluser = $membership_instance->with('user')->get();
    
        return response()->json([
            'data' => $alluser
        ]);
    }
    

    public function activeTrails(){
        $locations = Locations::all();
        return view('admin_dashboard.leads.active_trials',compact('locations'));
    }

    public function getActiveTrialsMembers(Request $request){
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;

        $membership_instance = MembershipInstances::query();

        if($startDate && $endDate){
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
    
            $membership_instance->whereBetween('purchase_date_copy', [$start, $end]);
        }

        if($location != null){
            $membership_instance->whereHas('user.location', function ($q) use ($location) {
                $q->where('location_id', $location);
            });
        }

        $membershipstrail = MembershipTrial::where('status',1)->get();
       
        $membershipstrailnames = [];
        foreach($membershipstrail as $trial) {
            $membershipstrailnames[] = $trial->name;
        }

        $membership_instance->where(function ($query) use ($membershipstrailnames) {
            foreach ($membershipstrailnames as $trialName) {
                $query->orWhere('membership_name', 'LIKE', "%$trialName%");
            }
        });

        // $activeMemberships = ActiveMember::where('status',1)->get();

        // $activeMembershipsNames = [];
        // foreach($activeMembershipsNames as $active){
        //     $activeMembershipsNames[] = $active->name;
        // }

        // $membership_instance->where(function($query) use ($activeMembershipsNames){
        //     foreach($activeMembershipsNames as $name){
        //         $query->orWhere('membership_name', 'LIKE', "%$name%");
        //     }
        // });
        
        $membership_instance->where('status','active');
        $activetrials = $membership_instance->with('user')->get();

        return response()->json([
            'data' => $activetrials
        ]);
    }


    public function completeTrial(){
        $locations = Locations::all();
        return view('admin_dashboard.leads.complete_trial',compact('locations'));
    }

    public function getCompleteTrial(Request $request){
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
        $membership_instance = MembershipInstances::query();

        if($startDate && $endDate){
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
    
            $membership_instance->whereBetween('purchase_date_copy', [$start, $end]);
        }

        if($location != null){
            $membership_instance->whereHas('user.location', function ($q) use ($location) {
                $q->where('location_id', $location);
            });
        }
        
        $membershipstrail = MembershipTrial::where('status',1)->get();

        $membershipstrailnames = [];
        foreach($membershipstrail as $trial) {
            $membershipstrailnames[] = $trial->name;
        }

        $membership_instance->where(function ($query) use ($membershipstrailnames) {
            foreach ($membershipstrailnames as $trialName) {
                $query->orWhere('membership_name', 'LIKE', "%$trialName%");
            }
        });
    
        $completedTrials = $membership_instance->where('status','done')->with('user')->get();
    
        return response()->json([
            'data' => $completedTrials
        ]);
    }

}
