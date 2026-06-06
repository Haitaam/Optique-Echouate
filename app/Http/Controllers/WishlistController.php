<?php

namespace App\Http\Controllers;

use App\Features\Products\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();
        $items = Wishlist::with('product.categories')
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('pages.account.wishlist', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $customer = auth('customer')->user();

        $exists = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $data['product_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Déjà dans vos favoris'], 409);
        }

        Wishlist::create([
            'customer_id' => $customer->id,
            'product_id' => $data['product_id'],
        ]);

        $count = Wishlist::where('customer_id', $customer->id)->count();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Ajouté aux favoris',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Ajouté aux favoris');
    }

    public function destroy(Product $product)
    {
        $customer = auth('customer')->user();

        Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->delete();

        $count = Wishlist::where('customer_id', $customer->id)->count();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Retiré des favoris',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Retiré des favoris');
    }
}
