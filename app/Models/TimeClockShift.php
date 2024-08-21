<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeClockShift extends Model
{
    use HasFactory;

    protected $appends = ['regular_pay_amount','instructor_pay_amount'];

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
        return $this->duration * $payRate;
    }

    public function getInstructorPayAmountAttribute(){
        $payRate = $this->employee_payrate->instructor_pay ?? 0;
        return $this->duration * $payRate;
    }

}
