<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();
        $ordersCount = Order::where('customer_id', $customer->id)->count();
        $latestOrders = Order::where('customer_id', $customer->id)
            ->withCount('orderItems')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.account.index', compact('ordersCount', 'latestOrders'));
    }

    public function orders()
    {
        $customer = auth('customer')->user();
        $orders = Order::where('customer_id', $customer->id)
            ->withCount('orderItems')
            ->latest()
            ->paginate(10);

        return view('pages.account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $customer = auth('customer')->user();

        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        $order->load('orderItems.product');
        $paymentMethods = self::PAYMENT_METHODS;

        return view('pages.account.order-detail', compact('order', 'paymentMethods'));
    }

    public function orderItems(Order $order)
    {
        $customer = auth('customer')->user();

        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        $order->load('orderItems.product');

        return response()->json([
            'items' => $order->orderItems->map(fn($oi) => [
                'product_id' => $oi->product_id,
                'name' => $oi->product?->name ?? '#' . $oi->product_id,
                'price' => (float) $oi->price,
                'quantity' => $oi->quantity,
                'image' => $oi->product?->image,
            ]),
        ]);
    }

    public function reviews()
    {
        $customer = auth('customer')->user();
        $reviews = Review::with('product')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        $reviewableOrders = Order::where('customer_id', $customer->id)
            ->whereIn('status', [Order::STATUS_DELIVERED])
            ->withCount('orderItems')
            ->latest()
            ->get();

        return view('pages.account.reviews', compact('reviews', 'reviewableOrders'));
    }

    public function storeReview(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|max:2000',
        ]);

        $customer = auth('customer')->user();

        $exists = Review::where('customer_id', $customer->id)
            ->where('product_id', $data['product_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Vous avez déjà donné un avis sur ce produit.');
        }

        Review::create([
            'product_id' => $data['product_id'],
            'customer_id' => $customer->id,
            'order_id' => $data['order_id'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'is_approved' => false,
        ]);

        return back()->with('success', 'Merci pour votre avis ! Il sera visible après modération.');
    }

    public function addresses()
    {
        return view('pages.account.addresses');
    }

    const PAYMENT_METHODS = [
        'bank_transfer' => 'Virement bancaire (BMCE Bank)',
    ];
}
