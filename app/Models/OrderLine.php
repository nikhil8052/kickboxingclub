<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;

    public function order(){
        return $this->belongsTo(Orders::class, 'order_id', 'order_id'); 
    }

    public function membership_instance(){
        return $this->hasOne(MembershipInstances::class,'membership_id','membership_instance_id');
    }

    public function membership_transaction(){
        return $this->hasOne(MembershipTransaction::class,'membership_transactions_id','transaction_id');
    }
}
