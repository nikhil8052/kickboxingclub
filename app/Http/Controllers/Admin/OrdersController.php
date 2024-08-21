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

        $completedSaleCount = 0;
        $RefundsCount = 0;
        $partiallyRefundCount = 0;
        $CancelledCount = 0;
        $pendingCount = 0;
        $paymentFailuerCount = 0;

        $orderswL = $query->with('orderlines')->get();
        foreach ($orderswL as $order) {
        
            if( $order->status == 'Completed' ){
                $completedSaleCount++;
                $totalcompletedSale += $order->total;
            }

            if($order->status == 'Partially Refunded' ){
                $partiallyRefundCount++;
                $totalRefundedAmountPartiall += $order->total_amount_refunded;
                $totalcompletedSale += $order->total;
            }
            if( $order->status == 'Refunded' ){
                $RefundsCount++;
                $totalRefunds += $order->total_amount_refunded;
            }
            if($order->status == 'Cancelled' ){
                $CancelledCount++;
                $totalcancelled += $order->total;
            }

            if($order->status == 'Payment Failure'){
                $paymentFailuerCount++;
                $totalPF += $order->total;
            }

            if($order->status == 'Pending' || $order->status == 'Deferred'  ){
                $pendingCount++;
                $totalPending += $order->total;
            }
            // foreach ($order->orderlines as $orderline) {
            //     if( $orderline->status == 'Completed' ){
            //         $completedSaleCount++;
            //         $totalcompletedSale += $orderline->line_total;
            //     }
    
            //     if($orderline->status == 'Partially Refunded' ){
            //         $partiallyRefundCount++;
            //         $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
            //         $totalRefundedAmountPartiall += $orderdata->total_amount_refunded;
            //         $totalcompletedSale += $orderline->line_total;
            //     }
            //     if( $orderline->status == 'Refunded' ){
            //         $RefundsCount++;
            //         $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
            //         $totalRefunds += $orderdata->total_amount_refunded;
            //     }
            //     if($orderline->status == 'Cancelled' ){
            //         $CancelledCount++;
            //         $totalcancelled += $orderline->line_total;
            //     }

            //     if($orderline->status == 'Payment Failure'){
            //         $paymentFailuerCount++;
            //         $totalPF += $orderline->line_total;
            //     }

            //     if($orderline->status == 'Pending' || $orderline->status == 'Deferred'  ){
            //         $pendingCount++;
            //         $totalPending += $orderline->line_total;
            //     }
            // }
        }

        $masterArray = [
            'totalcompletedSale' => formatCurrency($totalcompletedSale - $totalRefundedAmountPartiall),
            'totalRefunds' => formatCurrency($totalRefunds + $totalRefundedAmountPartiall),
            'totalPending'  => formatCurrency($totalPending),
            'totalcancelled' => formatCurrency($totalcancelled),
            'totalPF'  => formatCurrency($totalPF),
            'completedSaleCount' => $completedSaleCount + $partiallyRefundCount,
            'RefundsCount'  => $RefundsCount + $partiallyRefundCount,
            'CancelledCount' => $CancelledCount,
            'paymentFailuerCount' => $paymentFailuerCount,
            'pendingCount' => $pendingCount
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

        $query = Orders::query();

        $locations = Locations::all();
    
        if ($startDate && $endDate) {
            $query->whereBetween(DB::raw('date_created'), [Carbon::parse($startDate), Carbon::parse($endDate)]);
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

        $completedSaleCount = 0;
        $RefundsCount = 0;
        $partiallyRefundCount = 0;
        $CancelledCount = 0;
        $pendingCount = 0;
        $paymentFailuerCount = 0;

        $orderswL = $query->with('orderlines')->get();
        foreach ($orderswL as $order) {
        
            if( $order->status == 'Completed' ){
                $completedSaleCount++;
                $totalcompletedSale += $order->total;
            }

            if($order->status == 'Partially Refunded' ){
                $partiallyRefundCount++;
                $totalRefundedAmountPartiall += $order->total_amount_refunded;
                $totalcompletedSale += $order->total;
            }
            if( $order->status == 'Refunded' ){
                $RefundsCount++;
                $totalRefunds += $order->total_amount_refunded;
            }
            if($order->status == 'Cancelled' ){
                $CancelledCount++;
                $totalcancelled += $order->total;
            }

            if($order->status == 'Payment Failure'){
                $paymentFailuerCount++;
                $totalPF += $order->total;
            }

            if($order->status == 'Pending' || $order->status == 'Deferred'  ){
                $pendingCount++;
                $totalPending += $order->total;
            }
            // foreach ($order->orderlines as $orderline) {
            //     if( $orderline->status == 'Completed' ){
            //         $completedSaleCount++;
            //         $totalcompletedSale += $orderline->line_total;
            //     }
    
            //     if($orderline->status == 'Partially Refunded' ){
            //         $partiallyRefundCount++;
            //         $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
            //         $totalRefundedAmountPartiall += $orderdata->total_amount_refunded;
            //         $totalcompletedSale += $orderline->line_total;
            //     }
            //     if( $orderline->status == 'Refunded' ){
            //         $RefundsCount++;
            //         $orderdata = Orders::Where('order_id',$orderline->order_id)->first();
            //         $totalRefunds += $orderdata->total_amount_refunded;
            //     }
            //     if($orderline->status == 'Cancelled' ){
            //         $CancelledCount++;
            //         $totalcancelled += $orderline->line_total;
            //     }

            //     if($orderline->status == 'Payment Failure'){
            //         $paymentFailuerCount++;
            //         $totalPF += $orderline->line_total;
            //     }

            //     if($orderline->status == 'Pending' || $orderline->status == 'Deferred'  ){
            //         $pendingCount++;
            //         $totalPending += $orderline->line_total;
            //     }
            // }
        }

        $totalcompletedSalecheck =  $query->sum('total');

        $masterArray = [
            'totalcompletedSale' => formatCurrency($totalcompletedSale - $totalRefundedAmountPartiall),
            'totalRefunds' => formatCurrency($totalRefunds + $totalRefundedAmountPartiall),
            'totalPending'  => formatCurrency($totalPending),
            'totalcancelled' => formatCurrency($totalcancelled),
            'totalPF'  => formatCurrency($totalPF),
            'completedSaleCount' => $completedSaleCount + $partiallyRefundCount,
            'RefundsCount'  => $RefundsCount + $partiallyRefundCount,
            'CancelledCount' => $CancelledCount,
            'paymentFailuerCount' => $paymentFailuerCount,
            'pendingCount' => $pendingCount,
            'totalcompletedSalecheck' => $totalcompletedSalecheck
        ];  
    
        return response()->json(['masterArray' => $masterArray]);
    }
    
    function formatCurrenc_y($amount) {
        $amount = number_format($amount, 2, '.', ',');
        $parts = explode('.', $amount);
        $rupees = $parts[0];
        $paise = isset($parts[1]) ? $parts[1] : '00';

        if(strlen($rupees) === 6){
            $rupees = preg_replace('/\B(?=(\d{2})+(?=\d{3}))/u',',', $rupees);
        } 

        if(strlen($rupees) === 5){
            $rupees = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $rupees);
        } 
        return $rupees . '.' . $paise;
    } 

    function formatCurrency($amount) {
        $amount = str_replace(',', '', $amount);
        
        $parts = explode('.', $amount);
        $rupees = $parts[0];
        $paise = isset($parts[1]) ? $parts[1] : '00';
        $formattedRupees = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $rupees);
    
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
}
