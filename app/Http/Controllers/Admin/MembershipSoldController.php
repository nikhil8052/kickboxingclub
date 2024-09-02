<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTrial;
use App\Models\Commission;
use App\Models\SoldMembership;
use App\Models\Locations;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MembershipSoldController extends Controller
{
    public function SoldMembership()
    {
        $membership_types = Commission::where('status',true)->get();
        $membershipstrail = MembershipTrial::all();
        $locations = Locations::all();
        $soldmemberships = SoldMembership::where('employee_id',auth()->user()->id)->get();
        return view('admin_dashboard.membership_sold.membership_sold',compact('soldmemberships','membershipstrail','membership_types','locations')); 
    }

    public function SoldMembershipStats()
    {
        $membership_types = Commission::where('status',true)->get();
        $membershipstrail = MembershipTrial::all();
        $locations = Locations::all();
        $soldmemberships = SoldMembership::where('employee_id',auth()->user()->id)->orderBy('sold_date', 'desc')->get();
        return view('admin_dashboard.membership_sold.stats',compact('soldmemberships','membershipstrail','membership_types','locations')); 
    }

    public function GetSoldMembershipStats(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $location = $request->location;
        
        $query = SoldMembership::query();

        $query->where('employee_id',auth()->user()->id);

        if ($startDate && $endDate) {
            $query->whereBetween('sold_date', [$startDate, $endDate]);
        }

        if(isset($location) && count($location) > 0) {
            $query->whereIn('location_id',$location);
        }

        $records = $query->with(['membershiptype','location'])->get();

        $records = $query->with(['membershiptype', 'location', 'user'])->get();
    
        $starter = ['count' => 0, 'billing' => 0];
        $club = ['count' => 0, 'billing' => 0];
        $vip = ['count' => 0, 'billing' => 0];
        $gold = ['count' => 0, 'billing' => 0];
        $total = ['count' => 0, 'billing' => 0];
    
        foreach ($records as $data) {
            if (isset($data->membershiptype)) {
                switch ($data->membershiptype->type) {
                    case 'Starter':
                        $starter['count'] += 1;
                        $starter['billing'] += $data->monthly_billing;
                        break;
    
                    case 'Club':
                        $club['count'] += 1;
                        $club['billing'] += $data->monthly_billing;
                        break;
    
                    case 'VIP':
                        $vip['count'] += 1;
                        $vip['billing'] += $data->monthly_billing;
                        break;
    
                    case 'Gold':
                        $gold['count'] += 1;
                        $gold['billing'] += $data->monthly_billing;
                        break;
    
                    default:
                        break;
                }
    
                $total['count'] += 1;
                $total['billing'] += $data->monthly_billing;
            }
        }
    
        $starter['billing'] = number_format($starter['billing'], 2);
        $club['billing'] = number_format($club['billing'], 2);
        $vip['billing'] = number_format($vip['billing'], 2);
        $gold['billing'] = number_format($gold['billing'], 2);
        $total['billing'] = number_format($total['billing'], 2);

        $masterArray = [
            'records' => $records,
            'Mstarter' => $starter,
            'Mclub' => $club,
            'Mvip' => $vip,
            'Mgold' => $gold,
            'Mtotal' => $total,
        ];  
    
        return response()->json(['masterArray' => $masterArray]);
    }

    public function MembershipSoldAddProcc(Request $request)
    {
        $request->validate([
            'm_name' => 'required',
            'membership_type' => 'required',
            'weekly_billing' => 'required'
        ]);

        if($request->id) {
            $data = SoldMembership::find($request->id);

            if($data) {
                $data->name = $request->m_name;
                $data->membership_typeId = $request->membership_type;
                $data->weekly_billing = $request->weekly_billing;
                $data->monthly_billing = $request->monthly_billing;
                $data->employee_id = auth()->user()->id;
                $data->location_id = $request->location;
                $data->sold_by = auth()->user()->full_name;
                $data->trial_id = $request->trial;
                $data->save();
    
                return redirect()->back()->with('success',' Data Updated successfully');
            } else {
                return redirect()->back()->with('error','Something went Wrong');
            }

        } else {
            $data = new SoldMembership();
            $data->name = $request->m_name;
            $data->membership_typeId = $request->membership_type;
            $data->weekly_billing = $request->weekly_billing;
            $data->monthly_billing = $request->monthly_billing;
            $data->employee_id = auth()->user()->id;
            $data->sold_by = auth()->user()->full_name;
            $data->location_id = $request->location;
            $data->trial_id = $request->trial;
            $data->save();

            return redirect()->back()->with('success',' Data Added successfully');
        }
    }

    public function SoldMembershipremove($id){
        $data = SoldMembership::find($id);
        if($data){
            $data->delete();
            return redirect()->back()->with('success','record deleted successfully');
        } else {
            return redirect()->back()->with('error','something went wrong');
        }
    }

    public function AllSoldMemberships()
    {
        $membership_types = Commission::where('status',true)->get();
        $membershipstrail = MembershipTrial::all();
        $locations = Locations::all();
        $soldmemberships = SoldMembership::where('employee_id',auth()->user()->id)->get();
        return view('admin_dashboard.membership_sold.overall_stats',compact('soldmemberships','membershipstrail','membership_types','locations')); 
    }

    public function GetOverallStats(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $location = $request->location;
    
        $query = SoldMembership::query();
    
        if ($startDate && $endDate) {
            $query->whereBetween('sold_date', [$startDate, $endDate]);
        }
    
        if (isset($location) && count($location) > 0) {
            $query->whereIn('location_id', $location);
        }
    
        $records = $query->with(['membershiptype', 'location', 'user'])->get();
    
        $starter = ['count' => 0, 'billing' => 0];
        $club = ['count' => 0, 'billing' => 0];
        $vip = ['count' => 0, 'billing' => 0];
        $gold = ['count' => 0, 'billing' => 0];
        $total = ['count' => 0, 'billing' => 0];
    
        $employeeStats = [];
    
        foreach ($records as $data) {
            if (isset($data->membershiptype)) {
                switch ($data->membershiptype->type) {
                    case 'Starter':
                        $starter['count'] += 1;
                        $starter['billing'] += $data->monthly_billing;
                        break;
    
                    case 'Club':
                        $club['count'] += 1;
                        $club['billing'] += $data->monthly_billing;
                        break;
    
                    case 'VIP':
                        $vip['count'] += 1;
                        $vip['billing'] += $data->monthly_billing;
                        break;
    
                    case 'Gold':
                        $gold['count'] += 1;
                        $gold['billing'] += $data->monthly_billing;
                        break;
    
                    default:
                        break;
                }
    
                $total['count'] += 1;
                $total['billing'] += $data->monthly_billing;
            }
        }
    
        $starter['billing'] = number_format($starter['billing'], 2);
        $club['billing'] = number_format($club['billing'], 2);
        $vip['billing'] = number_format($vip['billing'], 2);
        $gold['billing'] = number_format($gold['billing'], 2);
        $total['billing'] = number_format($total['billing'], 2);

        $allEmployees  = User::where('is_admin',2)->get();
    
        $groupedRecords = $records->groupBy('employee_id');

        $employeeStats = [];

        foreach ($allEmployees as $employee) {
            $employeeStats[$employee->id] = [
                'employee_name' => $employee->name,
                'starter' => 0,
                'club' => 0,
                'vip' => 0,
                'gold' => 0,
                'total' => 0,
                'commision' => 0
            ];
        }
    
        foreach ($groupedRecords as $employee_id => $employeeRecords) {
            $Estarter = 0;
            $Eclub = 0;
            $Evip = 0;
            $Egold = 0;
            $Etotal = 0;
            $commision = 0;
    
            foreach ($employeeRecords as $index => $data) {
              
                if (isset($data->membershiptype)) {
                    switch ($data->membershiptype->type) {
                        case 'Starter':
                            $Estarter += 1;
                            break;
    
                        case 'Club':
                            $Eclub += 1;
                            break;
    
                        case 'VIP':
                            $Evip += 1;
                            break;
    
                        case 'Gold':
                            $Egold+= 1;
                            break;
    
                        default:
                            break;
                    }
    
                    $Etotal += 1;
                    $commision += $data->membershiptype->amount;
                }
            }
    
            $commision  = number_format( $commision , 2);
    
            if (isset($employeeStats[$employee_id])) {
                $employeeStats[$employee_id]['starter'] = $Estarter;
                $employeeStats[$employee_id]['club'] = $Eclub;
                $employeeStats[$employee_id]['vip'] = $Evip;
                $employeeStats[$employee_id]['gold'] = $Egold;
                $employeeStats[$employee_id]['total'] = $Etotal;
                $employeeStats[$employee_id]['commision'] = $commision;
            }
        }
    
        $masterArray = [
            'records' => $records,
            'Mstarter' => $starter,
            'Mclub' => $club,
            'Mvip' => $vip,
            'Mgold' => $gold,
            'Mtotal' => $total,
            'employeeStats' => $employeeStats  
        ];
    
        return response()->json(['masterArray' => $masterArray]);
    }
}
