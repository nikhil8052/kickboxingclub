<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
    use HasFactory;

    public function group(){
        return $this->hasOne(Group::class, 'group_id','group_id');
    }
}
