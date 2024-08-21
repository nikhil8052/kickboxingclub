<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Orders;
use App\Models\Locations;
use App\Models\MembershipInstances;
use Carbon\Carbon;
use App\Models\AllUsers;
use App\Models\Employees;
use App\Models\EmployeePayRate;

use Illuminate\Support\Facades\DB;

class EmployeeStatController extends Controller
{

    public function Employees()
    {
        return view('admin_dashboard.employees.index');
    }

    public function getEmployees(Request $request){
        $allemployees = Employees::with(['user.location','payrate'])->get();
        return response()->json($allemployees);
    }


    public function addPayRates(){
        return view('admin_dashboard.employees.add_pay_rates');
    }

    public function payRateProcc(Request $request){
        if($request->employee_id != null){
            $employeePayRate = EmployeePayRate::where('employee_id',$request->employee_id)->first();
            $employeePayRate->regular_pay = $request->regular_pay;
            $employeePayRate->instructor_pay = $request->instructor_pay;
            $employeePayRate->status = 1;
            $employeePayRate->update();
        }
        
        return redirect()->back()->with('success','Pay Rates updated successfully');
    }
}



