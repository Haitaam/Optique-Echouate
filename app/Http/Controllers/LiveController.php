<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use App\Features\Products\Models\Product;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    public function __construct(
        private SettingsService $settingsService,
    ) {}

    public function hash()
    {
        $productHash = md5(Product::max('updated_at') . Product::count());
        $orderHash = md5(Order::max('updated_at') . Order::count());
        $notifHash = md5(Notification::max('created_at') . Notification::unread()->count());
        $settingsHash = md5($this->settingsService->get('updated_at', 'none'));

        return response()->json([
            'products' => $productHash,
            'orders' => $orderHash,
            'notifications' => $notifHash,
            'settings' => $settingsHash,
            'combined' => md5($productHash . $orderHash . $notifHash . $settingsHash),
            'ts' => now()->timestamp,
        ]);
    }

    public function product($id)
    {
        $product = Product::with('categories')->findOrFail($id);
        return response()->json($product->toArray());
    }

    public function order($id)
    {
        $order = Order::with('customer')->findOrFail($id);
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'badge_class' => $order->badge_class,
            'status_icon' => $order->status_icon,
            'updated_at' => $order->updated_at,
        ]);
    }

    public function admin()
    {
        $pendingOrders = Order::pendingConfirmation()->count();
        $unreadNotifications = Notification::unread()->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_CONFIRMED])
            ->sum('total_price');
        $revenueToday = Order::whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_CONFIRMED])
            ->whereDate('created_at', today())
            ->sum('total_price');

        return response()->json([
            'pending_orders' => $pendingOrders,
            'unread_notifications' => $unreadNotifications,
            'total_orders' => $totalOrders,
            'total_revenue' => number_format($totalRevenue, 2, ',', ' ') . ' MAD',
            'revenue_today' => number_format($revenueToday, 2, ',', ' ') . ' MAD',
            'ts' => now()->timestamp,
        ]);
    }

    public function settings()
    {
        return response()->json($this->settingsService->all());
    }
}
