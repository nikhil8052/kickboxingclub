<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldMembership extends Model
{
    use HasFactory;

    public function membershiptype(){
        return $this->hasOne(Commission::class,'id','membership_typeId');
    }

    public function trial(){
        return $this->hasOne(MembershipTrial::class,'id','trial_id');
    } 

    public function location(){
        return $this->hasOne(Locations::class,'location_id','location_id');
    } 

    public function user(){
        return $this->hasOne(User::class,'id','employee_id');
    } 
}
