<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeClockShift extends Model
{
    use HasFactory;

    public function location(){
        return $this->hasOne(Locations::class,'location_id','location_id');
    }

    public function employee(){
        return $this->hasOne(Employees::class,'employee_id','employee_id');
    }
}
