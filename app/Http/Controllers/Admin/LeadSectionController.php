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

class LeadSectionController extends Controller
{
    public function index(){
        return view('admin_dashboard.leads.index');
    }

    public function getTrialsPurchased(){
        $membership_instance = MembershipInstances::query();
        $membershipstrail = MembershipTrial::all();

        $membershipstrailnames = [];
        foreach($membershipstrail as $trial) {
            $membershipstrailnames[] = $trial->name;
        }

        $membership_instance->where(function ($query) use ($membershipstrailnames) {
            foreach ($membershipstrailnames as $trialName) {
                $query->orWhere('membership_name', 'LIKE', "%$trialName%");
            }
        });

        // $membership_instance->select('user_id', DB::raw('count(user_id) as total_count'))
        //     ->groupBy('user_id');

        $membership_instance->whereIn('status',['pending_customer_activation']);
        $trials = $membership_instance->with('user')->get();

        return response()->json($trials);
    }

    // public function getTrialsPurchased(){
    //     $membershipstrailnames = MembershipTrial::where('status', 1)->pluck('name')->toArray();
    //     $purchasedTrials = MembershipInstances::where(function ($query) use ($membershipstrailnames) {
    //             foreach ($membershipstrailnames as $trialName) {
    //                 $query->orWhere('membership_name', 'LIKE', "%$trialName%");
    //             }
    //         })
    //         ->whereIn('status', ['pending_customer_activation','pending_start_date','done','terminated','frozen','payment_failure','cancelled','ding_failure'])
    //         ->select('user_id', DB::raw('count(user_id) as total_count'))
    //         ->groupBy('user_id')
    //         ->pluck('user_id')->toArray();

    

    //     $activeMembers = MembershipInstances::whereIn('user_id', $purchasedTrials)
    //         ->where(function ($query) use ($membershipstrailnames) {
    //             foreach ($membershipstrailnames as $trialName) {
    //                 $query->orWhere('membership_name', 'LIKE', "%$trialName%");
    //             }
    //         })
    //         ->where('status','active')
    //         ->pluck('user_id')->toArray();
    

    //     $usersNotScheduled = array_diff($purchasedTrials, $activeMembers);
    //     $usersNotScheduledDetails = AllUsers::whereIn('user_id', $usersNotScheduled)->get();

    //     return response()->json($usersNotScheduledDetails);
    // }
    

    public function purchasedTrails(){
        return view('admin_dashboard.leads.purchased_trials');
    }

    public function activeTrails(){
        // $activeMembers = ActiveMember::where('status',1)->get();
        // $membership_instance = MembershipInstances::query();

        // $activeMemberTrials = [];

        // foreach($activeMembers as $trials){
        //     $activeMemberTrials[] = $trials->name;
        // }

        // // dd($activeMemberTrials);
        
        // $membership_instance->where(function ($query) use ($activeMemberTrials) {
        //     foreach ($activeMemberTrials as $trialName) {
        //         $query->orWhere('membership_name', 'LIKE', "%$trialName%");
        //     }
        // });

       

        // $membership_instance->select('user_id', DB::raw('count(user_id) as total_count'))
        //     ->groupBy('user_id');

        // $membership_instance->where('status','active');
        // $activetrials = $membership_instance->with('user')->get();

        // dd($activetrials);


        return view('admin_dashboard.leads.active_trials');
    }

    public function getActiveTrialsMembers(){
        // $activeMembers = ActiveMember::where('status',1)->get();
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
        
        $membership_instance->where('status','active');
        $activetrials = $membership_instance->with('user')->get();

        return response()->json($activetrials);

    }


    public function completeTrial(){
        $membershipstrailnames = MembershipTrial::where('status', 1)->pluck('name')->toArray();
        $completedTrials = MembershipInstances::where(function ($query) use ($membershipstrailnames) {
                foreach ($membershipstrailnames as $trialName) {
                    $query->orWhere('membership_name', 'LIKE', "%$trialName%");
                }
            })
            ->where('status', 'done')
            ->select('user_id', DB::raw('count(user_id) as total_count'))
            ->groupBy('user_id')
            ->pluck('user_id')->toArray();

        

        $nonMembers = MembershipInstances::whereIn('user_id', $completedTrials)
            ->where(function ($query) use ($membershipstrailnames) {
                foreach ($membershipstrailnames as $trialName) {
                    $query->orWhere('membership_name', 'LIKE', "%$trialName%");
                }
            })
            ->whereIn('status',['active'])
            ->pluck('user_id')->toArray();

        $usersNotMembers = array_diff($completedTrials, $nonMembers);
        
        $usersNotMembersDetails = AllUsers::whereIn('user_id', $usersNotMembers)->get();
        // dd($usersNotMembersDetails);

        return view('admin_dashboard.leads.complete_trial');
    }

    // public function getCompleteTrial(){
    //     $membership_instance = MembershipInstances::query();
    //     $membershipstrail = MembershipTrial::where('status',1)->get();

    //     $membershipstrailnames = [];
    //     foreach($membershipstrail as $trial) {
    //         $membershipstrailnames[] = $trial->name;
    //     }

    //     $membership_instance->where(function ($query) use ($membershipstrailnames) {
    //         foreach ($membershipstrailnames as $trialName) {
    //             $query->orWhere('membership_name', 'LIKE', "%$trialName%");
    //         }
    //     });
        
    //     $membership_instance->where('status','done');
    //     $membership_instance->select('user_id', DB::raw('count(user_id) as total_count'))
    //     ->groupBy('user_id');

    //     $completetrials = $membership_instance->with('user')->get();

    //     return response()->json($completetrials);
    // }

    public function getCompleteTrial(){
        $membershipstrailnames = MembershipTrial::where('status', 1)->pluck('name')->toArray();
        $completedTrials = MembershipInstances::where(function ($query) use ($membershipstrailnames) {
                foreach ($membershipstrailnames as $trialName) {
                    $query->orWhere('membership_name', 'LIKE', "%$trialName%");
                }
            })
            ->where('status', 'done')
            ->select('user_id', DB::raw('count(user_id) as total_count'))
            ->groupBy('user_id')
            ->pluck('user_id')->toArray();

        

        $nonMembers = MembershipInstances::whereIn('user_id', $completedTrials)
            ->where(function ($query) use ($membershipstrailnames) {
                foreach ($membershipstrailnames as $trialName) {
                    $query->orWhere('membership_name', 'LIKE', "%$trialName%");
                }
            })
            ->whereIn('status',['active'])
            ->pluck('user_id')->toArray();

        $usersNotMembers = array_diff($completedTrials, $nonMembers);
        
        $usersNotMembersDetails = AllUsers::whereIn('user_id', $usersNotMembers)->get();

        return response()->json($usersNotMembersDetails);
    }
}
