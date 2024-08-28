<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllUsers extends Model
{
    use HasFactory;

    public function location(){
        return $this->hasOne(Locations::class,'location_id','home_location_id');
    }

    public function membership(){
        return $this->belongsTo(MembershipInstances::class,'user_id','user_id');
    }

    public function getDateJoinedAttribute($value){
        return formatDate($value);
    }
    
    public function getWaiverSignedDatetimeAttribute($value){
        return formatDate($value);
    } 
}
