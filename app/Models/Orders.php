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
    
    
    public function getDatePlacedAttribute($value){

        return formatDate($value);

    }
}
