<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\MembershipTrial;
use App\Models\ActiveMember;
use Hash;

class SettingController extends Controller
{
    public function index(){
        $user = Users::where('is_admin',1)->first();
        $membershipTrails = MembershipTrial::where('status',1)->get();
        $activeMembers = ActiveMember::where('status',1)->get();
        return view('admin_dashboard.settings.index',compact('user','membershipTrails','activeMembers'));
    }

    public function updateCredentials(Request $request){
        if(isset($request->username) && $request->username != null){
            $password = Hash::make($request->password);

            $user = Users::where('is_admin',1)->first();
            $user->email = $request->username;
            $user->password = $password;
            $user->update();

            return redirect()->back()->with('success','Credentials updated');
        }
    }

    public function updateTrials(Request $request){
        if(isset($request->id)){
            $requestedIds = $request->id;  
            $membershipTrails = MembershipTrial::whereNotIn('id', $requestedIds)->get();
            
            foreach($membershipTrails as $trials){
                $trials->status = 0;
                $trials->update();
            }   
            
            return response()->json(['success'=>true]);
        }        
    }

    public function updateActiveMembers(Request $request){
        if(isset($request->id)){
            $requestedIds = $request->id;
            $activeMembers = ActiveMember::whereNotIn('id',$requestedIds)->get();

            foreach($activeMembers as $member){
                $member->status = 0;
                $member->update();
            }

            return response()->json(['success'=>true]);
        }
    }

    public function addTrials(Request $request){
        if(isset($request->name)){
            $membershipTrails = new MembershipTrial;
            $membershipTrails->type = 'trial';
            $membershipTrails->name = $request->name;
            $membershipTrails->status = 1;
            $membershipTrails->save();

            return redirect()->back()->with('success','Successfully Add Trials Sold');
        }
    }

    public function addActiveMembers(Request $request){
        if(isset($request->name)){
            $activeMembers = new ActiveMember;
            $activeMembers->type = 'exclude';
            $activeMembers->name = $request->name;
            $activeMembers->status = 1;
            $activeMembers->save();

            return redirect()->back()->with('success','Successfully Add Daily Active Members');
        }
    }
}
