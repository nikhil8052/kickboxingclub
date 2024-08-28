<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;


    // protected $casts = [
    //     'date_placed' => ReadableNumber::class,
    // ];  

    // public function orderline(){
    //     return $this->hasOne(OrderLine::class,'order_line_id','order_line_id');
    // }

    public function orderlines()
    {
        return $this->hasMany(OrderLine::class, 'order_id','order_id');
    }
    
    
    public function getDatePlacedAttribute($value){

        return formatDate($value);

    }

    public function user(){
        return $this->hasOne(AllUsers::class, 'user_id','user_id');
    }
}
