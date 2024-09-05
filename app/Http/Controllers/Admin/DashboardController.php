<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\Orders;
use App\Models\Locations;
use App\Models\MembershipInstances;
use Carbon\Carbon;
use App\Models\AllUsers;
use App\Models\MembershipTrial;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function Dashboard(Request $request)
    {
        $locations = Locations::all();
        $locationId = $request->input('location');
        $location = Locations::find($locationId);

        if ($request->ajax()) {

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

            $TotalsalesCompleted = Orders::query();
            $TotalsalesRefunded = Orders::query();
            $orderlineQuery= Orders::query();

            $userquery = AllUsers::query();

            $queryOrders = Orders::query();
            $queryMemberships = MembershipInstances::query();
            $forcastedSales = MembershipInstances::query();
            $queryCancelMemberships = MembershipInstances::query();
            $TrialSoldMemberships = MembershipInstances::query();
            $visitorsdata = AllUsers::query();


            
            if ($startDate && $endDate) {
                $TotalsalesCompleted->whereBetween(DB::raw('date_created_copy'), [Carbon::parse($startDate), Carbon::parse($endDate)]);
                $TotalsalesRefunded->whereBetween(DB::raw('refund_date_created_copy'), [Carbon::parse($startDate), Carbon::parse($endDate)]);
                $orderlineQuery->whereBetween(DB::raw('date_created_copy'), [Carbon::parse($startDate), Carbon::parse($endDate)]);

                $queryOrders->whereBetween(DB::raw('date_created_copy'), [$startDate, $endDate]);
                $queryMemberships->whereBetween('purchase_date_copy', [$startDate, $endDate]);
                $forcastedSales->whereBetween('next_charge_date_display', [$startDate, $endDate]);
                $queryCancelMemberships->whereBetween('end_date_copy', [$startDate, $endDate]);
                $TrialSoldMemberships->whereBetween('purchase_date_copy', [$startDate, $endDate]);
                $visitorsdata->whereBetween(DB::raw('STR_TO_DATE(date_joined, "%Y-%m-%d")'), [$startDate, $endDate]);

                $userquery->whereBetween('date_joined', [$startDate, $endDate]);
            }

            if($location) {
                $TotalsalesCompleted->where('location',$location->name);
                $TotalsalesRefunded->where('location',$location->name);
                $orderlineQuery->where('location',$location->name);

                $queryOrders->where('location', $location->name);
                $forcastedSales->where('purchase_location_id', $location->location_id);
                $queryMemberships->where('purchase_location_id', $location->location_id);
                $queryCancelMemberships->where('purchase_location_id', $location->location_id);
                $TrialSoldMemberships->where('purchase_location_id', $location->location_id);
                $visitorsdata->where('home_location_id', $location->location_id);

                $userquery->whereHas('location', function ($q) use ($location) {
                    $q->where('location_id', $location);
                });
            }

            $totalcompletedSale  = $TotalsalesCompleted->whereIn('status',['Completed','Refunded','Partially Refunded'])->sum('total');
            $GetORderunitamount  = $TotalsalesCompleted->whereIn('status',['Refunded','Partially Refunded'])->sum('total');

            $totaloverAllsales = number_format($totalcompletedSale - $GetORderunitamount);

            $orderswL = $orderlineQuery->with('orderlines')->get();

            // $totalmembSale = 0 ;
            // $totalcreditsale =0;
            // $totalRefundedAmount = 0;
            // $totalRefundedAmountC = 0;
            $overthecounter = 0;
            $membershipbilling = 0;

            foreach ($orderswL as $order) {
            
                if($order->status == 'Completed' || $order->status == 'Partially Refunded') {
                    foreach ($order->orderlines as $orderline) {
                        if($orderline->transaction_type  == 'MembershipTransaction' && $orderline->status == 'Completed' ){
                            // $totalmembSale += $orderline->line_total;
                            if($orderline->membership_instance && $orderline->membership_instance->renewal_count > 1){
                                $membershipbilling += $orderline->line_total;
                            } else {
                                $overthecounter += $orderline->line_total;

                            }
                        }
            
                        // if($orderline->transaction_type  == 'MembershipTransaction' && $orderline->status == 'Partially Refunded' ){
                        //     $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
                        //     $totalRefundedAmount += $orderdata->total_amount_refunded;
                        //     $totalmembSale += $orderline->line_total;
                        // }
                        // if( $orderline->transaction_type  == 'CreditTransaction' && $orderline->status == 'Completed' ){
                        //     $totalcreditsale += $orderline->line_total;
                        // }
                        // if($orderline->transaction_type  == 'CreditTransaction' && $orderline->status == 'Partially Refunded' ){
                        //     $orderdataC = Orders::Where('order_id',$orderline->order_id)->first();
                        //     $totalRefundedAmountC += $orderdataC->total_amount_refunded;
                        //     $totalcreditsale += $orderline->line_total;
                        // }
                    }
                }
            }

            // $totalcreditSales = number_format($totalcreditsale - $totalRefundedAmountC);
            // $totalMembershipSales = number_format($totalmembSale - $totalRefundedAmount);

        
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
            $activeMemberships = $queryMemberships->clone()->whereIn('status',[ 'active','pending_customer_activation','pending_start_date'])->count();
            $cancelledMemberships = $queryCancelMemberships->whereIn('status', ['active','cancelled', 'terminated','payment_failure','ding_failure'])->count();

        
            $activeMembers = $userquery->whereHas('memberships', function ($query) {
                $query->where('status', 'active');
            })->count();


            $membershipstrail = MembershipTrial::all();
            $membershipstrailnames = [];
            foreach($membershipstrail as $trial) {
                $membershipstrailnames[] = $trial->name;
            }
            $TrialSoldMembershipsCount = $TrialSoldMemberships->where(function ($query) use ($membershipstrailnames) {
                foreach ($membershipstrailnames as $trialName) {
                    $query->orWhere('membership_name', 'LIKE', "%$trialName%");
                }
            })->count();

            
            $allvisitors = $visitorsdata->count();
            
            // Forcasted sales counts 
            $forcastedSales->where(function ($qy) use ($membershipstrailnames) {
                    foreach ($membershipstrailnames as $trialName) {
                        $qy->orWhere('membership_name', 'NOT LIKE', "%$trialName%");
                    }
                });
            $forcastedSales = $forcastedSales->where('status','active')->sum('renewal_rate');



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
                // 'totalMembershipSales' => $totalMembershipSales,
                // 'totalcreditSales' => $totalcreditSales,
                'totaloverAllsales' => $totaloverAllsales,
                'forcastedSales'=>$forcastedSales,
                'membershipbilling' => $membershipbilling,
                'overthecounter' => $overthecounter,
                'activeMembers' => $activeMembers
            ]);
        }


        return view('admin_dashboard.home.index', [
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

    public function Users()
    {
        $locations = Locations::all();
        return view('admin_dashboard.users.index',compact('locations'));
    }

    public function GetUsers(Request $request)
    {
        $query = AllUsers::query();
        $query = $query->where('type', 'users');
    
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $location = $request->location_id;
    
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
    
            $query->whereBetween('date_joined', [$start, $end]);
        }
    
        // if ($location != null) {
        //     $query->whereHas('location', function ($q) use ($location) {
        //         $q->where('location_id', $location);
        //     });
        // }
    
        // $allusers =  $query->whereHas('memberships', function ($query,$location) {
        //     $query->where('status', 'active')->where('purchase_location_id',$location);
        // })->with(['location','memberships'])->get();
        if ($location !== null) {
            $allusers = $query->whereHas('memberships', function ($query) use ($location) {
                $query->where('status', 'active')
                      ->where('purchase_location_id', $location);
            })->with(['location', 'memberships'])->get();
        } else {
            $allusers = $query->whereHas('memberships', function ($query) {
                $query->where('status', 'active');
            })->with(['location', 'memberships'])->get();
        }
        
    
        return response()->json([
            'data' => $allusers
        ]);
    }
}
