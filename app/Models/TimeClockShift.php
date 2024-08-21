<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeClockShift extends Model
{
    use HasFactory;

    protected $appends = ['regular_pay_amount'];

    public function location(){
        return $this->hasOne(Locations::class,'location_id','location_id');
    }

    public function employee(){
        return $this->hasOne(Employees::class,'employee_id','employee_id');
    }

    public function employee_payrate(){
        return $this->hasOne(EmployeePayRate::class, 'employee_id', 'employee_id');
    }


    public function getRegularPayAmountAttribute(){
        $payRate = $this->employee_payrate->regular_pay ?? 0;
        $totalSeconds = $this->duration;
        $hours = intdiv($totalSeconds, 3600); 
        $minutes = intdiv($totalSeconds % 3600, 60); 
        $seconds = $totalSeconds % 60; 
        $durationInHours = $hours + ($minutes / 60) + ($seconds / 3600);
    
        return $durationInHours * $payRate;
    }
    

}
