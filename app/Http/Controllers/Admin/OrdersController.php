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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{

    public function Dashboard(Request $request)
    {
        $Totalsalessum = Orders::where('status', 'completed')->sum('total');
        $totalsales = formatCurrency($Totalsalessum);

        $forecastrecord = MembershipInstances::sum('renewal_rate');
        $forecastsales = formatCurrency($forecastrecord);

        $locations = Locations::all();

        $locationId = $request->input('location');
        $location = Locations::find($locationId);

        $date = $request->input('dates');
        if ($date) {
            $dates = explode(' - ', $date);
            $startDateString = $dates[0];
            $endDateString = $dates[1];

            $startDate = Carbon::parse($startDateString);
            $endDate = Carbon::parse($endDateString);
        } else {
            $startDate = null;
            $endDate = null;
        }

        $queryOrders = Orders::query();
        $queryMemberships = MembershipInstances::query();
        $queryCancelMemberships = MembershipInstances::query();
        $TrialSoldMemberships = MembershipInstances::query();
        $visitorsdata = AllUsers::query();

        if ($startDate && $endDate) {
            $queryOrders->whereBetween(DB::raw('STR_TO_DATE(date_placed, "%Y-%m-%d")'), [$startDate, $endDate]);
            $queryMemberships->whereBetween('purchase_date_copy', [$startDate, $endDate]);
            // $queryCancelMemberships->whereBetween(DB::raw('STR_TO_DATE(cancellation_datetime, "%Y-%m-%d")'), [$startDate, $endDate]);
            $queryCancelMemberships->whereBetween('end_date', [$startDate, $endDate]);
            $TrialSoldMemberships->whereBetween('purchase_date_copy', [$startDate, $endDate]);
            $visitorsdata->whereBetween(DB::raw('STR_TO_DATE(date_joined, "%Y-%m-%d")'), [$startDate, $endDate]);
        }

        if ($location) {
            $queryOrders->where('location', $location->name);
            $queryMemberships->where('purchase_location_id', $location->location_id);
            $queryCancelMemberships->where('purchase_location_id', $location->location_id);
            $TrialSoldMemberships->where('purchase_location_id', $location->location_id);
            $visitorsdata->where('home_location_id', $location->location_id);
        }

        $failedPayments = $queryOrders->clone()
            ->where('status', 'Payment Failure')
            ->selectRaw('COUNT(*) as count')
            ->value('count');


        $completedPayments = $queryOrders->clone()
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as count')
            ->value('count');


        $pendingPayments = $queryOrders->clone()
            ->where('status', 'Pending')
            ->selectRaw('COUNT(*) as count')
            ->value('count');


        $totalMemberships = $queryMemberships->count();

        $activeMemberships = $queryMemberships->clone()->where('status', 'active')
            ->count();
        // $activeMemberships = $queryMemberships->clone()->count();

        $cancelledMemberships = $queryCancelMemberships->whereIn('status', ['cancelled', 'terminated','payment_failure','ding_failure'])
            ->count();

        // $activeMemberships = $activeMemberships - $cancelledMemberships;
        
        $TrialSoldMembershipsCount = $TrialSoldMemberships->where(DB::raw('STR_TO_DATE(start_date, "%Y-%m-%d")'), '>', DB::raw('STR_TO_DATE(purchase_date, "%Y-%m-%d")'))->where('status',['active','pending_start_date','pending_customer_activation'])->count();
        // $allvisitors = 0;
        // foreach($visitorsdata as $visit){
        //     $membership = MembershipInstances::where('user_id',$visit->user_id)->first();
        //     if(!$membership) {
        //         $allvisitors +=1;
        //     }
        // }
        $allvisitors = $visitorsdata->count();
        if ($request->ajax()) {
            return response()->json([
                'failedPayments' => $failedPayments,
                'completedPayments' => $completedPayments,
                'pendingPayments' => $pendingPayments,
                'totalMemberships' => $totalMemberships,
                'activeMemberships' => $activeMemberships,
                'cancelledMemberships' => $cancelledMemberships,
                'TrialSoldMembershipsCount' => $TrialSoldMembershipsCount,
                'allvisitors' => $allvisitors,
                'locations' => $locations
            ]);
        }

        return view('admin_dashboard.home.index', [
            'totalsales' => $totalsales,
            'forecastsales' => $forecastsales,
            'failedPayments' => $failedPayments,
            'completedPayments' => $completedPayments,
            'pendingPayments' => $pendingPayments,
            'totalMemberships' => $totalMemberships,
            'activeMemberships' => $activeMemberships,
            'cancelledMemberships' => $cancelledMemberships,
            'TrialSoldMembershipsCount' => $TrialSoldMembershipsCount,
            'allvisitors' => $allvisitors,
            'locations' => $locations,
        ]);
    }


    public function salesData()
    {
        
        $dates = Orders::select(
                DB::raw('MIN(date_placed) as min_date'),
                DB::raw('MAX(date_placed) as max_date')
            )
            ->where('status', 'completed')
            ->first();

        $minDate = $dates->min_date;
        $maxDate = $dates->max_date;

        $minYear = date('Y', strtotime($minDate));
        $maxYear = date('Y', strtotime($maxDate));

        $salesData = Orders::select(
            'location',
            DB::raw('YEAR(date_placed) as year'),
            DB::raw('MONTH(date_placed) as month'),
            DB::raw('SUM(total) as total_sales')
        )
        ->where('status', 'completed')
        ->groupBy('location', DB::raw('YEAR(date_placed)'), DB::raw('MONTH(date_placed)'))
        ->get()
        ->groupBy('location')
        ->mapWithKeys(function ($locationGroup, $location) use ($minYear, $maxYear) {
            
            $monthlySales = $locationGroup->mapWithKeys(function ($item) {
                $month = str_pad($item->month, 2, '0', STR_PAD_LEFT);
                $year = $item->year;
                return ["{$year}-{$month}" => $item->total_sales];
            })->toArray();

            $allMonths = collect();
            for ($year = $minYear; $year <= date('Y'); $year++) {
                for ($month = 1; $month <= 12; $month++) {
                    $monthKey = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);
                    $allMonths[$monthKey] = $monthlySales[$monthKey] ?? 0;
                }
            }

            return [$location => $allMonths];
        });

        return response()->json(['sales' => $salesData]);
    }

    public function Orders()
    {
        return view('admin_dashboard.orders.orders2');
    }

    public function SalesStats()
    {
        return view('admin_dashboard.orders.orders');

    }

    public function TotalSales(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
    
        $query = Orders::query();

        $locations = Locations::all();
    
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(date_placed, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $alldataSums = $query->where('status', 'completed')
            ->select('location')
            ->selectRaw('SUM(total) as total')
            ->groupBy('location')
            ->get()
            ->keyBy('location')
            ->mapWithKeys(fn($item) => [$item->location => round($item->total)]);

        $query = Orders::query();
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(date_placed, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $FailedPayments = $query->where('status', 'Payment Failure')
            ->select('location')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('location')
            ->get()
            ->keyBy('location')
            ->mapWithKeys(fn($item) => [$item->location => $item->count]);
    
        $query = Orders::query();
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(date_placed, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $CompletePayments = $query->where('status', 'completed')
            ->select('location')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('location')
            ->get()
            ->keyBy('location')
            ->mapWithKeys(fn($item) => [$item->location => $item->count]);
    
        $query = Orders::query();
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(date_placed, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $PendingPayments = $query->where('status', 'Pending')
            ->select('location')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('location')
            ->get()
            ->keyBy('location')
            ->mapWithKeys(fn($item) => [$item->location => $item->count]);
    
        $query = MembershipInstances::query();
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(purchase_date, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $forecastrecord = $query->groupBy('purchase_location_id')
            ->select('purchase_location_id', DB::raw('SUM(renewal_rate) as total_renewal_rate'))
            ->get()
            ->pluck('total_renewal_rate', 'purchase_location_id')
            ->mapWithKeys(function ($sum, $locationId) {
                $locationName = Locations::find($locationId)->name;
                return [$locationName => $sum];
            });
    
        $query = MembershipInstances::query();
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(purchase_date, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $activeMembershiprecord = $query->where('status', 'active')
            ->pluck('purchase_location_id')
            ->filter()
            ->countBy();
        $activeMembership = Locations::whereIn('location_id', $activeMembershiprecord->keys())
            ->pluck('name', 'location_id')
            ->mapWithKeys(fn($name, $id) => [$name => $activeMembershiprecord[$id]]);
    
        $query = MembershipInstances::query();
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(purchase_date, "%Y-%m-%d")'), [$startDate, $endDate]);
        }
    
        $cancelledMembershiprecord = $query->whereIn('status', ['cancelled', 'terminated'])
            ->pluck('purchase_location_id')
            ->filter()
            ->countBy();
        $cancelledMembership = Locations::whereIn('location_id', $cancelledMembershiprecord->keys())
            ->pluck('name', 'location_id')
            ->mapWithKeys(fn($name, $id) => [$name => $cancelledMembershiprecord[$id]]);
    
        $masterArray = [];
    
        foreach ($locations as $location) {
            if (!isset($masterArray[$location->name])) {
                $masterArray[$location->name] = [
                    'alldataSum' => 0,
                    'failedPayments' => 0,
                    'completePayments' => 0,
                    'pendingPayments' => 0,
                    'forecast' => 0,
                    'activeMembership' => 0,
                    'cancelledMembership' => 0,
                ];
            }
        }
    
        foreach ($alldataSums as $location => $total) {
            $masterArray[$location]['alldataSum'] = formatCurrency($total) ;
        
        }

        foreach ($FailedPayments as $location => $count) {
            $masterArray[$location]['failedPayments'] = formatCurrency($count);
        }
    
        foreach ($CompletePayments as $location => $count) {
            $masterArray[$location]['completePayments'] =  formatCurrency($count);
        }
    
        foreach ($PendingPayments as $location => $count) {
            $masterArray[$location]['pendingPayments'] =  formatCurrency($count);
        }
    
        foreach ($forecastrecord as $location => $total_renewal_rate) {
            $masterArray[$location]['forecast'] =  formatCurrency($total_renewal_rate);
        }
    
        foreach ($activeMembership as $location => $count) {
            $masterArray[$location]['activeMembership'] = formatCurrency($count);
        }
    
        foreach ($cancelledMembership as $location => $count) {
            $masterArray[$location]['cancelledMembership'] = formatCurrency($count);
        }
    
        return response()->json(['masterArray' => $masterArray]);
    }
    
    function formatCurrency($amount) {
        $amount = number_format($amount, 2, '.', '');
        $parts = explode('.', $amount);
        $rupees = $parts[0];
        $paise = isset($parts[1]) ? $parts[1] : '00';

        if(strlen($rupees) === 6){
            // $rupees = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $rupees);
            $rupees = preg_replace('/\B(?=(\d{2})+(?=\d{3}))/u',',', $rupees);
        } 

        if(strlen($rupees) === 5){
            $rupees = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $rupees);
            // $rupees = preg_replace('/\B(?=(\d{2})+(?=\d{3}))/u',',', $rupees);
        }
        
        return $rupees . '.' . $paise;
    } 
    
    public function GetOrders(Request $request)
    {
        $allorders = Orders::where('type','orders')->get();
        return response()->json($allorders);
    }
  
    public function Instances()
    {
        return view('admin_dashboard.memberships_instances.index');
    }

    public function GetInstances(Request $request)
    {
        $allinstances = MembershipInstances::where('type','membership_instances')->with('user')->get();
        return response()->json($allinstances);
    }

    public function Users()
    {
        return view('admin_dashboard.users.index');
    }

    public function GetUsers(Request $request)
    {
        $allusers = AllUsers::where('type','users')->get();
        return response()->json($allusers);
    }

    public function Employees()
    {
        return view('admin_dashboard.employees.index');
    }

    public function GetEmployees(Request $request)
    {
        $allemployees = Employees::where('type','employees')->get();
        return response()->json($allemployees);
    }

}
