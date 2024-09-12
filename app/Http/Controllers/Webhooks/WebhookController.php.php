<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\MembershipInstances;
use App\Models\OrderLine;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $data = $request->all();
            savelog("Marientek webhook all the data", "WebhookController.php", $data);
            $event = $request->input('event'); // Assuming the event type is passed in 'event' field

            // Check the webhook action
            if ($data['action'] === 'order.completed') {
                $this->handleOrderCompleted($data['order']);
            } elseif ($data['action'] === 'order.refunded') {
                $this->handleOrderRefunded($data['order']);
            } elseif ($data['action'] === 'membership.activated' || $data['action'] === 'membership.deactivated' || $data['action'] === 'membership.deactivated') {
                $this->updateMembership($data['membership']);
            }

            savelog("Webhook completed the work...", "WebhookController.php", $data);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            savelog($e->getMessage(), "WebhookController.php");
        }
    }

    //  order completed
    protected function handleOrderCompleted($orderData)
    {
        try {
            // Order Data
            $orderId = $orderData['id'];
            $status = $orderData['type'];
            $currency = $orderData['currency'];
            $customerData = $orderData['customer'];
            $customerId = $customerData['id'];
            $discount_total = $orderData['discount_total'];
            $discounts = $orderData['discounts'];
            $location = $orderData['location'];
            $number = $orderData['number'];
            $order_datetime = $orderData['order_datetime'];
            $order_lines = $orderData['order_lines'];
            $payment_sources = $orderData['payment_sources'];
            $refund_sources = $orderData['refund_sources'];
            $refund_total = $orderData['refund_total'];
            $status = $orderData['status'];
            $subtotal = $orderData['subtotal'];
            $tax_total = $orderData['tax_total'];
            $taxes = $orderData['taxes'];
            $subtotal = $orderData['subtotal'];
            $total = $orderData['total'];
            // End order data

            // Find the order and update the order data first
            $order = Order::where('order_id', $orderId)->first();
            if (!$order) {
                $msg = " Order Not found with the Order ID : " . $orderId;
                savelog($msg);
            }

            // Update the order data with the new data
            $order->currency = $currency;
            $order->discounts = json_encode($discounts);
            $order->payment_sources = json_encode($payment_sources) ?? null;
            $order->refund_sources = json_encode($refund_sources) ?? null;
            $order->refund_total = $refund_total;
            $order->status = $status;
            $order->subtotal = $subtotal;
            $order->tax_total = $tax_total;
            $order->taxes = json_encode($taxes) ?? null;
            $order->total = $total;
            $order->save();

            // Now update the data of the order lines
            $order_line_id = $order_lines['id'];
            $order_line = OrderLine::where('order_line_id', $order_line_id)->first();
            if (!$order_line) {
                $msg = " Order Line Not found with the Order ID : " . $order_line_id;
                savelog($msg);
            }
            $order_line->line_subtotal = $order_lines['line_subtotal'];
            $order_line->line_total = $order_lines['line_total'];
            $order_line->options = $order_lines['options'];
            $order_line->product_id = $order_lines['product']['id'] ?? $order_line['product_id'];
            $order_line->product_type = $order_lines['product']['product_type'] ?? $order_line['product_type'];
            $order_line->quantity = $order_lines['quantity'];
            $order_line->status = $order_lines['status'];
            $order_line->unit_subtotal = $order_lines['unit_subtotal'];
            $order_line->unit_total = $order_lines['unit_total'];
            $order_line->save();

            Log::info('Order completed successfully', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            savelog($e->getMessage(), "WebhookController.php");
        }
    }

    //  order refunded
    protected function handleOrderRefunded($orderData)
    {
        try {
            // Order Data
            $orderId = $orderData['id'];
            $status = $orderData['type'];
            $currency = $orderData['currency'];
            $customerData = $orderData['customer'];
            $customerId = $customerData['id'];
            $discount_total = $orderData['discount_total'];
            $discounts = $orderData['discounts'];
            $location = $orderData['location'];
            $number = $orderData['number'];
            $order_datetime = $orderData['order_datetime'];
            $order_lines = $orderData['order_lines'];
            $payment_sources = $orderData['payment_sources'];
            $refund_sources = $orderData['refund_sources'];
            $refund_total = $orderData['refund_total'];
            $status = $orderData['status'];
            $subtotal = $orderData['subtotal'];
            $tax_total = $orderData['tax_total'];
            $taxes = $orderData['taxes'];
            $subtotal = $orderData['subtotal'];
            $total = $orderData['total'];
            // End order data

            // Find the order and update the order data first
            $order = Order::where('order_id', $orderId)->first();
            if (!$order) {
                $msg = " Order Not found with the Order ID : " . $orderId;
                savelog($msg);
            }

            // Update the order data with the new data
            $order->currency = $currency;
            $order->discounts = json_encode($discounts);
            $order->payment_sources = json_encode($payment_sources) ?? null;
            $order->refund_sources = json_encode($refund_sources) ?? null;
            $order->refund_total = $refund_total;
            $order->status = $status;
            $order->subtotal = $subtotal;
            $order->tax_total = $tax_total;
            $order->taxes = json_encode($taxes) ?? null;
            $order->total = $total;
            $order->save();

            // Now update the data of the order lines
            $order_line_id = $order_lines['id'];
            $order_line = OrderLine::where('order_line_id', $order_line_id)->first();
            if (!$order_line) {
                $msg = " Order Line Not found with the Order ID : " . $order_line_id;
                savelog($msg);
            }
            $order_line->line_subtotal = $order_lines['line_subtotal'];
            $order_line->line_total = $order_lines['line_total'];
            $order_line->options = $order_lines['options'];
            $order_line->product_id = $order_lines['product']['id'] ?? $order_line['product_id'];
            $order_line->product_type = $order_lines['product']['product_type'] ?? $order_line['product_type'];
            $order_line->quantity = $order_lines['quantity'];
            $order_line->status = $order_lines['status'];
            $order_line->unit_subtotal = $order_lines['unit_subtotal'];
            $order_line->unit_total = $order_lines['unit_total'];
            $order_line->save();

            $msg = " Order updated successfully Order ID :" . $order->id;
            savelog($msg);
        } catch (\Exception $e) {
            savelog($e->getMessage(), "handle order refund function WebhookController.php");
        }
    }

    protected function updateMembership($membershipData)
    {
        try {
            $membership_id = $membershipData['id'];
            $membership_instance = MembershipInstances::where('membership_id', $membership_id)->first();
            $membership_instance->end_date = $membershipData['end_date'];
            $membership_instance->end_date_copy = convertToUSATimezone($membershipData['end_date']);
            $membership_instance->purchase_date = $membershipData['purchase_date'];
            $membership_instance->purchase_date_copy = convertToUSATimezone($membershipData['purchase_date']);
            $membership_instance->start_date = $membershipData['start_date'];
            $membership_instance->start_date_copy = convertToUSATimezone($membershipData['	start_date_copy']);
            $membership_instance->status = $membershipData['status'];
            $membership_instance->save();
        } catch (\Exception $e) {
            savelog($e->getMessage(), "handle order refund function WebhookController.php");
        }
    }
}
