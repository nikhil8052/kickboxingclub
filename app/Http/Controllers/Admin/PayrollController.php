<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeClockShift;
use App\Models\Locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function Payroll()
    {
        $alldata = TimeClockShift::all();
        $locations = Locations::all();
        return view('admin_dashboard.payroll.payroll',compact('alldata','locations'));
    }

    public function GetPayroll(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $location = $request->location_id;

        $alldata = TimeClockShift::query();

        if($request->location_id){
            $alldata->where('location_id',$request->location_id)->get();
        }

        if($startDate && $endDate){
            $start_d=Carbon::parse($startDate);
            $end_d=Carbon::parse($endDate);
            $alldata->where(function ($q) use ($start_d,$end_d ) {
                $q->whereBetween('start_datetime', [$start_d, $end_d])
                  ->orWhereBetween('end_datetime', [$start_d, $end_d]);
            });
        }

        $shifts = $alldata->with(['employee.user'])->with('location')->get();
        
        return response()->json([
            'shifts' => $shifts
         ]);
    }

    public function getEmployeesPayrollDetails(Request $request){
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $user_id = $request->id;
        $location = $request->location_id;

        $query = TimeClockShift::query();

        if($user_id){
            $query->whereHas('employee.user', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        if($location){
            $query->where('location_id',$location)->get();
        }

        if($startDate && $endDate){
            $start_d=Carbon::parse($startDate);
            $end_d=Carbon::parse($endDate);
            $query->where(function ($q) use ($start_d,$end_d ) {
                $q->whereBetween('start_datetime', [$start_d, $end_d])
                  ->orWhereBetween('end_datetime', [$start_d, $end_d]);
            });
        }

        $employeeDetails = $query->with(['employee.user'])->with('location')->get();
        
        return response()->json([
            'success' => true,
            'details' => $employeeDetails
        ]);
    }

    public function PayrollStates(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = TimeClockShift::query();

        $locations = Locations::all();

        if($request->location_id){
            $query->where('location_id',$request->location_id)->get();
        }

        if($startDate && $endDate){
            $start_d=Carbon::parse($startDate);
            $end_d=Carbon::parse($endDate);
            $query->where(function ($q) use ($start_d,$end_d ) {
                $q->whereBetween('start_datetime', [$start_d, $end_d])
                  ->orWhereBetween('end_datetime', [$start_d, $end_d]);
            });
        }

        $shifts = $query->select('employee_id')
            ->selectRaw('COUNT(*) as total_shifts')
            ->selectRaw('SUM(duration) as total_duration_seconds')
            ->groupBy('employee_id')
            ->with(['employee.user'])
            ->get();
        // dd($shifts);
        if ($request->ajax()) {
            return response()->json([
               'shifts' => $shifts
            ]);
        }
        return view('admin_dashboard.payroll.payroll_states',compact('shifts','locations'));
    }
}
