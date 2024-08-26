<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingCycle extends Model
{
    use HasFactory;

    public function membershipInstance(){
        return $this->hasOne(MembershipInstances::class,'membership_id','membership_instance_id');
    }

    public function locations(){
        return $this->hasOne(Locations::class,'location_id','location_id');
    }
}
