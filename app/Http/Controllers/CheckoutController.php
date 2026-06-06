<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Notification;
use App\Features\Products\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class CheckoutController extends Controller
{
    const PAYMENT_METHODS = [
        'bank_transfer' => 'Virement bancaire (BMCE Bank)',
    ];

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

        $orderItems = [];
        $totalPrice = 0;

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
                    ->with('checkout_error', "Seulement {$product->stock} unité(s) de « {$product->name} » sont actuellement disponibles en stock. Veuillez ajuster votre quantité pour continuer.")
                    ->with('checkout_fields', $request->except('items'));
            }

            $orderItems[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
                'brand' => $product->brand,
            ];

            $totalPrice += (float) $product->price * $quantity;
        }

        $paymentMethod = $validated['payment_method'] ?? 'bank_transfer';

        $customer = null;
        $email = $validated['email'];
        $tempPassword = null;

        if ($email) {
            $customer = Customer::where('email', $email)->first();

            if (!$customer) {
                $tempPassword = Str::random(12);

                $customer = Customer::create([
                    'name' => $validated['name'],
                    'email' => $email,
                    'phone' => $validated['phone'],
                    'password' => Hash::make($tempPassword),
                ]);

                try {
                    Mail::raw(
                        "Bonjour {$validated['name']},\n\n"
                        . "Un compte client a été créé automatiquement chez Optique Échouate.\n\n"
                        . "Voici vos identifiants :\n"
                        . "Email : {$email}\n"
                        . "Mot de passe temporaire : {$tempPassword}\n\n"
                        . "Connectez-vous pour suivre vos commandes : " . route('login') . "\n\n"
                        . "Merci de votre confiance !\n"
                        . "L'équipe Optique Échouate",
                        function ($message) use ($email, $validated) {
                            $message->to($email, $validated['name'])
                                ->subject('Votre compte Optique Échouate');
                        }
                    );
                } catch (\Exception $e) {
                    // Email sending is best-effort; continue even if it fails
                }
            }
        }

        $order = DB::transaction(function () use ($validated, $orderItems, $totalPrice, $cartItems, $paymentMethod, $customer) {
            $order = Order::create([
                'session_id' => session()->getId(),
                'customer_id' => $customer ? $customer->id : null,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'status' => Order::STATUS_PENDING,
                'total_price' => $totalPrice,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'type' => 'normal',
                'items' => $orderItems,
            ]);

            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', (int) ($item['quantity'] ?? 1));

                    if ($product->stock <= 0) {
                        Notification::create([
                            'type' => 'out_of_stock',
                            'title' => 'Rupture de stock : ' . $product->name,
                            'body' => 'Stock épuisé après la commande #' . $order->id,
                            'url' => route('admin.glasses.edit', $product),
                            'read' => false,
                        ]);
                    } elseif ($product->stock <= ($product->min_stock_threshold ?: 3)) {
                        Notification::create([
                            'type' => 'low_stock',
                            'title' => 'Stock faible : ' . $product->name,
                            'body' => 'Il ne reste que ' . $product->stock . ' unité(s) en stock.',
                            'url' => route('admin.glasses.edit', $product),
                            'read' => false,
                        ]);
                    }
                }
            }

            Notification::create([
                'type' => 'new_order',
                'title' => 'Nouvelle commande #' . $order->id,
                'body' => $validated['name'] . ' — ' . number_format($totalPrice, 2, ',', ' ') . ' MAD',
                'url' => route('admin.orders.show', $order),
                'read' => false,
            ]);

            return $order;
        });

        if ($customer) {
            auth('customer')->login($customer);
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
            ->with('order_confirmed', true)
            ->with('temp_password', $tempPassword);
    }
}
