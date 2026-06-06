<?php

namespace App\Http\Controllers\Admin;

use App\Features\Products\Models\Product;
use App\Features\Categories\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Testimonial;
use Illuminate\Support\Facades\File;

class DashboardController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $productCount = Product::count();
        $categoriesCount = Category::count();
        $testimonialsCount = Testimonial::count();
        $pendingOrders = Order::pendingConfirmation()->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_CONFIRMED])->sum('total_price');

        $revenueToday = Order::whereDate('created_at', today())
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_CONFIRMED])
            ->sum('total_price');

        $revenueMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_CONFIRMED])
            ->sum('total_price');

        $ordersByStatus = [];
        foreach (Order::STATUSES as $status) {
            $ordersByStatus[$status] = Order::where('status', $status)->count();
        }

        $lowStockProducts = Product::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock_threshold')
            ->count();

        $outOfStockProducts = Product::where('stock', '<=', 0)->count();

        $allProducts = Product::all();
        $validImages = $allProducts->filter(fn($p) => $p->imageExists())->count();
        $brokenImages = $productCount - $validImages;
        $imagesOnDisk = collect(File::allFiles(public_path('images')))
            ->filter(fn($f) => in_array($f->getExtension(), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
            ->count();

        $recentProducts = Product::with('categories')->latest()->take(5)->get();
        $recentTestimonials = Testimonial::latest()->take(5)->get();

        $brands = Brand::orderBy('name')->pluck('name');
        $brandCounts = Product::selectRaw('brand, count(*) as total')
            ->groupBy('brand')
            ->pluck('total', 'brand');

        return view('admin.dashboard.index', compact(
            'productCount', 'categoriesCount', 'testimonialsCount', 'validImages', 'brokenImages', 'imagesOnDisk',
            'recentProducts', 'recentTestimonials', 'brands', 'brandCounts',
            'pendingOrders', 'totalOrders', 'totalRevenue', 'revenueToday', 'revenueMonth',
            'ordersByStatus', 'lowStockProducts', 'outOfStockProducts'
        ));
    }
}
