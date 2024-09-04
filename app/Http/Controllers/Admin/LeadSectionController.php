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
        return view('admin_dashboard.leads.index');
    }

    public function getTrialsPurchased(){
        $timeClock = TimeClockShift::query();
        $timeClock->select('employee_id')->groupBy('employee_id');
        $classes = $timeClock->with('employee')->get();

        $userIDs = [];
        foreach($classes as $class){
            $userIDs[] = $class->employee->user_id;
        }
     
        $membership_instance = MembershipInstances::query();
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

        $membership_instance->whereNotIn('user_id',$userIDs);
        $membership_instance->whereIn('status',['active','done']);
        $alluser = $membership_instance->with('user')->get();
    
        return response()->json($alluser);
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
        
        $membership_instance->where('status','active');
        $activetrials = $membership_instance->with('user')->get();

        return response()->json([
            'data' => $activetrials
        ]);
    }


    public function completeTrial(){
        // $membership_instance = MembershipInstances::query();
        // $membershipstrail = MembershipTrial::where('status',1)->get();

        // $membershipstrailnames = [];
        // foreach($membershipstrail as $trial){
        //     $membershipstrailnames[] = $trial->name;
        // }

        // $membership_instance->where(function ($query) use ($membershipstrailnames) {
        //     foreach ($membershipstrailnames as $trialName) {
        //         $query->orWhere('membership_name', 'NOT LIKE', "%$trialName%");
        //     }
        // });

        // $membership_instance->where('status','active');
        // $activeMembers = $membership_instance->with('user')->get();

        // dd($activeMembers);

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
        
        $membership_instance->whereIn('status',['pending_customer_activation','pending_start_date']);
        $completetrials = $membership_instance->with('user')->get();

        return response()->json([
            'data' => $completetrials
        ]);
    }

    // public function getCompleteTrial(){
    //     $membershipstrailnames = MembershipTrial::where('status', 1)->pluck('name')->toArray();
    //     $completedTrials = MembershipInstances::where(function ($query) use ($membershipstrailnames) {
    //             foreach ($membershipstrailnames as $trialName) {
    //                 $query->orWhere('membership_name', 'LIKE', "%$trialName%");
    //             }
    //         })
    //         ->whereIn('status',['pending_start_date','pending_customer_activation'])
    //         ->select('user_id', DB::raw('count(user_id) as total_count'))
    //         ->groupBy('user_id')
    //         ->pluck('user_id')->toArray();

        

    //     $nonMembers = MembershipInstances::whereIn('user_id', $completedTrials)
    //         ->where(function ($query) use ($membershipstrailnames) {
    //             foreach ($membershipstrailnames as $trialName) {
    //                 $query->orWhere('membership_name', 'LIKE', "%$trialName%");
    //             }
    //         })
    //         ->where('status','active')
    //         ->pluck('user_id')->toArray();

    //     $usersNotMembers = array_diff($completedTrials, $nonMembers);
        
    //     $usersNotMembersDetails = AllUsers::whereIn('user_id', $usersNotMembers)->get();

    //     return response()->json($usersNotMembersDetails);
    // }
}
