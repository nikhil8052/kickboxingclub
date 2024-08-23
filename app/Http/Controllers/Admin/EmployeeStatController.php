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
        $locations = Locations::all();
        return view('admin_dashboard.employees.index',compact('locations'));
    }

    public function getEmployees(Request $request) {
        $query = Employees::query();
    
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
    
        if($startDate && $endDate){
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate) ;
    
            $query->whereHas('user', function($q) use ($start, $end) {
                $q->whereBetween('date_joined', [$start, $end]);
            });
        }
    
        if(!empty($location)){
            $query->whereHas('user.location', function ($q) use ($location) {
                $q->where('location_id', $location);
            });
        }
    
        $query->with(['user.location', 'payrate']);
    
        $employees = $query->paginate($request->length, ['*'], 'page', $request->start / $request->length + 1);
    
        return response()->json([
            'data' => $employees->items(),
            'recordsTotal' => $employees->total(),
            'recordsFiltered' => $employees->total(),
        ]);
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



