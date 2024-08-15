<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTransaction extends Model
{
    use HasFactory;

    public function membership_instance(){
        return $this->hasOne(MembershipInstances::class,'membership_id','membership_instances_id');
    }

    public function getTransactionDateTimeAttribute($value){
        return formatDate($value);
    }
}
