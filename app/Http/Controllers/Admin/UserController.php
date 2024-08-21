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
        if($request->id){
            $request->validate([
                'first_name' => 'required',
                'email' => 'required|email',
                // 'password' => 'required',
            ]);

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
                $currentPermissions = RolePermission::where('user_id', $user->id)->pluck('permission_id')->toArray();
            
                $permissionsToAdd = array_diff($request->permissions, $currentPermissions);
                
                $permissionsToRemove = array_diff($currentPermissions, $request->permissions);
            
                foreach($permissionsToAdd as $permission) {
                    $RP = new RolePermission();
                    $RP->user_id = $user->id;
                    $RP->permission_id = $permission;
                    $RP->save();
                }
            
                if (!empty($permissionsToRemove)) {
                    RolePermission::where('user_id', $user->id)
                        ->whereIn('permission_id', $permissionsToRemove)
                        ->delete();
                }
            }
            return redirect()->back()->with('success',' User Updated successfully');
        } else {

            $request->validate([
                'first_name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $olduser = User::where('email',$request->email)->first();
            if(!$olduser){
                $user = new User();
    
                $user->name = $request->first_name." ".$request->last_name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->is_admin = 0;
                $user->save();
    
                if($user && !empty($request->permissions)){
                    $currentPermissions = RolePermission::where('user_id', $user->id)->pluck('permission_id')->toArray();
                
                    $permissionsToAdd = array_diff($request->permissions, $currentPermissions);
                    
                    $permissionsToRemove = array_diff($currentPermissions, $request->permissions);
                
                    foreach($permissionsToAdd as $permission) {
                        $RP = new RolePermission();
                        $RP->user_id = $user->id;
                        $RP->permission_id = $permission;
                        $RP->save();
                    }
                
                    if (!empty($permissionsToRemove)) {
                        RolePermission::where('user_id', $user->id)
                            ->whereIn('permission_id', $permissionsToRemove)
                            ->delete();
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
