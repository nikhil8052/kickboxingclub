<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Models\Permission;
use App\Models\User;
use Hash;

class UserController extends Controller
{
    public function AddUser()
    {
        $permissions = Permission::all();
        $users = User::where('is_admin',0)->get();
        return view('admin_dashboard.add_users.index',compact('permissions','users'));
    }

    public function UserAddProcc(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($request->id){
            $user = User::find($request->id);
            if(!$user) {
                $olduser = User::where('email',$request->email)->first();
                if($olduser){
                    return redirect()->back()->with('error','user already registered');
                } else {
                    $user = new User();
                }
            } 
            $user->name = $request->first_name." ".$request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->is_admin = 0;
            $user->save();

            if($user && !empty($request->permissions)){
                foreach($request->permissions as $permission){
                    $exists = RolePermission::where('user_id', $user->id)
                    ->where('permission_id', $permission)
                    ->exists();
                    
                    if (!$exists) {
                        $RP = new RolePermission();
                        $RP->user_id = $user->id;
                        $RP->permission_id = $permission;
                        $RP->save();
                    }
                }
            }
            return redirect()->back()->with('success',' User Updated successfully');
        } else {
            $olduser = User::where('email',$request->email)->first();
            if(!$olduser){
                $user = new User();
    
                $user->name = $request->first_name." ".$request->last_name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->is_admin = 0;
                $user->save();
    
                if($user && !empty($request->permissions)){
                    foreach($request->permissions as $permission){
                        $exists = RolePermission::where('user_id', $user->id)
                        ->where('permission_id', $permission)
                        ->exists();
                        
                        if (!$exists) {
                            $RP = new RolePermission();
                            $RP->user_id = $user->id;
                            $RP->permission_id = $permission;
                            $RP->save();
                        }
                    }
                }
                return redirect()->back()->with('success','New User Created');
            } else {
                return redirect()->back()->with('error','user already registered');
            }
        }
    }

    public function UserRemove($id)
    {
        $user = User::find($id);
        if($user){
            $permissions = RolePermission::where('user_id',$user->id)->get();
            if($permissions){
                foreach($permissions as $p){
                    $p->delete();
                }
            }
            $user->delete();

            return redirect()->back()->with('success','user removed successfully');
        } else {
            return redirect()->back()->with('error','Something went wrong');   
        }
    }
}
