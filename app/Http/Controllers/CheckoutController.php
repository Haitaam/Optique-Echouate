<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Features\Products\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    const PAYMENT_METHODS = [
        'bank_transfer' => 'Virement bancaire (BMCE Bank)',
    ];

    public function __construct(
        private OrderService $orderService,
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'email' => 'required|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|json',
            'payment_method' => 'nullable|string|in:bank_transfer',
        ]);

        $cartItems = json_decode($validated['items'], true);

        if (!$cartItems || !is_array($cartItems) || count($cartItems) === 0) {
            return redirect()->route('products.index')
                ->with('checkout_error', 'Votre panier est vide.')
                ->with('checkout_fields', $request->except('items'));
        }

        $productIds = collect($cartItems)->pluck('id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cartItems as $item) {
            $product = $products->get($item['id']);

            if (!$product) {
                return redirect()->route('products.index')
                    ->with('checkout_error', "Le produit « " . ($item['name'] ?? '#' . $item['id']) . " » n'est plus disponible.")
                    ->with('checkout_fields', $request->except('items'));
            }

            $quantity = (int) ($item['quantity'] ?? 1);

            if ($product->stock < $quantity) {
                return redirect()->route('products.index')
                    ->with('checkout_error', "Seulement {$product->stock} unité(s) de « {$product->name} » sont actuellement disponibles en stock.")
                    ->with('checkout_fields', $request->except('items'));
            }
        }

        try {
            $order = $this->orderService->createFromCheckout($validated, $cartItems);
        } catch (\RuntimeException $e) {
            return redirect()->route('products.index')
                ->with('checkout_error', $e->getMessage())
                ->with('checkout_fields', $request->except('items'));
        }

        if ($order->customer_id) {
            auth('customer')->login($order->customer);
            session()->regenerate();
        }

        try {
            Mail::to($order->email, $order->name)->send(new OrderConfirmation($order));
        } catch (\Exception $e) {
            // Email is best-effort
        }

        $successMsg = 'Votre commande #' . $order->id . ' a été créée avec succès ! ';
        $successMsg .= 'Un email de confirmation vous a été envoyé.';

        return redirect()->route('account.orders')
            ->with('success', $successMsg)
            ->with('order_confirmed', true);
    }
}
