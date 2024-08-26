<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MembershipTransaction extends Model
{
    use HasFactory;

    public function user(){
        return $this->hasOne(AllUsers::class,'user_id','user_id');
    }

    public function locations(){
        return $this->hasOne(Locations::class,'location_id','purchase_location_id');
    }

    public function transaction(){
        return $this->hasOne(MembershipTransaction::class,'membership_instances_id','membership_id');
    }

    public function getPurchaseDateAttribute($value){
        return formatDate($value);
    }
    public function getStartDateAttribute($value){
        return formatDate($value);
    }

    public function getEndDateAttribute($value){
        return formatDate($value);
    }

}
