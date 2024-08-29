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
        $membershipTrails = MembershipTrial::all();
        $activeMembers = ActiveMember::all();
        return view('admin_dashboard.settings.index',compact('user','membershipTrails','activeMembers'));
    }

    public function updateCredentials(Request $request){
        $request->validate([
            'username' => 'required|email',
            'password' => 'required'
        ]);

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
        return $request->all();
    }
}
