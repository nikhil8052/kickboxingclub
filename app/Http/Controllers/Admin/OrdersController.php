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
use App\Models\OrderLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{

    public function Dashboard(Request $request)
    {
        $TotalsalesCompleted = Orders::where('status', 'completed')->orWhere('status','Partially Refunded')->sum('total');
        $TotalsalesPR = Orders::Where('status','Partially Refunded')->sum('total_amount_refunded');
        // $totaloverAllsales = formatCurrency($TotalsalesCompleted -  $TotalsalesPR );
        // dd($Totalsalessum);

        $GetORder = OrderLine::where('status', 'completed')->orWhere('status','Partially Refunded')->sum('line_total');
        $GetORderunit = OrderLine::Where('status','Partially Refunded')->get();
        $GetORderunitamount = 0;
        foreach($GetORderunit as $unit) {
            $TotalsalesPR = Orders::Where('order_id',$unit->order_id)->first();
            if($TotalsalesPR) {
                $GetORderunitamount += $TotalsalesPR->total_amount_refunded;
            }
        }

        $totaloverAllsales = formatCurrency($GetORder - $GetORderunitamount);

        $orderswL = Orders::with('orderlines')->get();
        $totalsale =0;
        $totalmembSale = 0 ;
        $totalcreditsale =0;
        $totalRefundedAmount = 0;
        $totalRefundedAmountC = 0;

        foreach ($orderswL as $order) {
        
            foreach ($order->orderlines as $orderline) {
                if($orderline->transaction_type  == 'MembershipTransaction' && $orderline->status == 'Completed' ){
                    $totalmembSale += $orderline->line_total;
                }
    
                if($orderline->transaction_type  == 'MembershipTransaction' && $orderline->status == 'Partially Refunded' ){
                    $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
                    $totalRefundedAmount += $orderdata->total_amount_refunded;
                    $totalmembSale += $orderline->line_total;
                }
        
        
                if( $orderline->transaction_type  == 'CreditTransaction' && $orderline->status == 'Completed' ){
                    $totalcreditsale += $orderline->line_total;
                }
                if($orderline->transaction_type  == 'CreditTransaction' && $orderline->status == 'Partially Refunded' ){
                    $orderdataC = Orders::Where('order_id',$orderline->order_id)->first();
                    $totalRefundedAmountC += $orderdataC->total_amount_refunded;
                    $totalcreditsale += $orderline->line_total;
                }
            }
        }
      
        $totalsales = formatCurrency($totalsale);
        $totalcreditSales = formatCurrency($totalcreditsale - $totalRefundedAmountC);
        $totalMembershipSales = formatCurrency($totalmembSale - $totalRefundedAmount);

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

        $cancelledMemberships = $queryCancelMemberships->whereIn('status', ['cancelled', 'terminated','payment_failure','ding_failure'])
            ->count();

        $TrialSoldMembershipsCount = $TrialSoldMemberships->where(DB::raw('STR_TO_DATE(start_date, "%Y-%m-%d")'), '>', DB::raw('STR_TO_DATE(purchase_date, "%Y-%m-%d")'))->where('status',['active','pending_start_date','pending_customer_activation'])->count();
        
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
                'locations' => $locations,
                'totalMembershipSales' => $totalMembershipSales,
                'totalcreditSales' => $totalcreditSales,
                'totaloverAllsales' => $totaloverAllsales
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
            'totalMembershipSales' => $totalMembershipSales,
            'totalcreditSales' => $totalcreditSales,
            'totaloverAllsales' => $totaloverAllsales
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
        $query = Orders::query();

        $locations = Locations::all();
    

        $totalcompletedSale = 0 ;
        $totalRefunds =0;
        $totalRefundedAmountPartiall = 0;
        $totalcancelled = 0;
        $totalPending = 0;
        $totalPF = 0;
        $orderswL = $query->with('orderlines')->get();
        foreach ($orderswL as $order) {
        
            foreach ($order->orderlines as $orderline) {
                if( $orderline->status == 'Completed' ){
                    $totalcompletedSale += $orderline->line_total;
                }
    
                if($orderline->status == 'Partially Refunded' ){
                    $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
                    $totalRefundedAmountPartiall += $orderdata->total_amount_refunded;
                    $totalcompletedSale += $orderline->line_total;
                }
                if( $orderline->status == 'Refunded' ){
                    $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
                    $totalRefunds += $orderdata->total_amount_refunded;
                }
                if($orderline->status == 'Cancelled' ){
                    $totalcancelled += $orderline->line_total;
                }

                if($orderline->status == 'Payment Failure'){
                    $totalPF += $orderline->line_total;
                }

                if($orderline->status == 'Pending' || $orderline->status == 'Deferred'  ){
                    $totalPending += $orderline->line_total;
                }
            }
        }

        $masterArray = [
            'totalcompletedSale' => formatCurrency($totalcompletedSale - $totalRefundedAmountPartiall),
            'totalRefunds' => formatCurrency($totalRefunds + $totalRefundedAmountPartiall),
            'totalPending'  => formatCurrency($totalPending),
            'totalcancelled' => formatCurrency($totalcancelled),
            'totalPF'  => formatCurrency($totalPF)

        ]; 

        return view('admin_dashboard.orders.orders2',compact('locations','masterArray'));
    }

    public function SalesStats()
    {
        return view('admin_dashboard.orders.orders');

    }

    public function TotalSales(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location;

        // $current_location = Locations::where('location_id',$location_id)->first();
    
        $query = Orders::query();

        $locations = Locations::all();
    
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('STR_TO_DATE(date_placed, "%Y-%m-%d")'), [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if($location) {
            $query->where('location',$location);
        }

        $totalcompletedSale = 0 ;
        $totalRefunds =0;
        $totalRefundedAmountPartiall = 0;
        $totalcancelled = 0;
        $totalPending = 0;
        $totalPF = 0;
        $orderswL = $query->with('orderlines')->get();
        foreach ($orderswL as $order) {
        
            foreach ($order->orderlines as $orderline) {
                if( $orderline->status == 'Completed' ){
                    $totalcompletedSale += $orderline->line_total;
                }
    
                if($orderline->status == 'Partially Refunded' ){
                    $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
                    $totalRefundedAmountPartiall += $orderdata->total_amount_refunded;
                    $totalcompletedSale += $orderline->line_total;
                }
                if( $orderline->status == 'Refunded' ){
                    $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
                    $totalRefunds += $orderdata->total_amount_refunded;
                }
                if($orderline->status == 'Cancelled' ){
                    $totalcancelled += $orderline->line_total;
                }

                if($orderline->status == 'Payment Failure'){
                    $totalPF += $orderline->line_total;
                }

                if($orderline->status == 'Pending' || $orderline->status == 'Deferred'  ){
                    $totalPending += $orderline->line_total;
                }
            }
        }

        $masterArray = [
            'totalcompletedSale' => formatCurrency($totalcompletedSale - $totalRefundedAmountPartiall),
            'totalRefunds' => formatCurrency($totalRefunds + $totalRefundedAmountPartiall),
            'totalPending'  => formatCurrency($totalPending),
            'totalcancelled' => formatCurrency($totalcancelled),
            'totalPF'  => formatCurrency($totalPF)

        ]; 
    
        return response()->json(['masterArray' => $masterArray]);
    }
    
    function formatCurrenc_y($amount) {
        $amount = number_format($amount, 2, '.', ',');
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
    function formatCurrency($amount) {
        // Remove any existing formatting
        $amount = str_replace(',', '', $amount);
        
        // Split the number into the integer and decimal parts
        $parts = explode('.', $amount);
        $rupees = $parts[0];
        $paise = isset($parts[1]) ? $parts[1] : '00';
        
        // Add commas in standard thousand separator format
        $formattedRupees = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $rupees);
    
        // Return the formatted amount with two decimal places
        return $formattedRupees . '.' . str_pad($paise, 2, '0');
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
