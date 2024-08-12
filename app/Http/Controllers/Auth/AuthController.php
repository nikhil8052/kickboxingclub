<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            if(Auth::user()->is_admin == 1){
                return redirect('/admin-dashboard')->with(['success'=>'Welcome to Admin Dashboard']);
            }else{
                return redirect('/logout');
            }  
        }else{
            return redirect()->back()->with(['error'=>"Login failed !!"]);
        }
    }

    public function adminLogout(){
        Auth::logout();

        return redirect('/')->with('success',"You have logged out succesfully");
    }
}
