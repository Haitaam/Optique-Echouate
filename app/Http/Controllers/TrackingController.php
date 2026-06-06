<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __invoke(Request $request)
    {
        $customer = auth('customer')->user();

        $orderId = $request->input('order_id');
        $order = null;
        $notFound = false;

        if ($orderId) {
            $id = (int) preg_replace('/[^0-9]/', '', $orderId);

            if ($id > 0) {
                $order = Order::where('id', $id)
                    ->where('customer_id', $customer->id)
                    ->first();

                if (!$order) {
                    $notFound = true;
                }
            } else {
                $notFound = true;
            }
        }

        $paymentMethods = CheckoutController::PAYMENT_METHODS;

        return view('pages.tracking', compact('order', 'notFound', 'paymentMethods'));
    }
}
