<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;

    public function user(){
        return $this->hasOne(AllUsers::class, 'user_id', 'user_id');
    }

    public function payrate(){
        return $this->hasOne(EmployeePayRate::class, 'employee_id', 'employee_id');
    }

    public function employeeGroup(){
        return $this->hasMany(EmployeeGroup::class, 'employee_id','employee_id');
    }

    public function shifts(){
        return $this->hasOne(TimeClockShift::class,'employee_id','employee_id');
    }
}
