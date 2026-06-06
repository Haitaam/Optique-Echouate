<?php

namespace App\Services;

use App\Features\Products\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private StockService $stockService,
        private NotificationService $notificationService,
    ) {}

    public function createFromCheckout(array $validated, array $cartItems): Order
    {
        $productIds = collect($cartItems)->pluck('id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $customer = $this->resolveCustomer($validated);
        $tempPassword = null;

        if ($customer && $customer->wasRecentlyCreated) {
            $tempPassword = $validated['_temp_password'] ?? null;
            $this->sendWelcomeEmail($customer, $tempPassword);
        }

        $order = DB::transaction(function () use ($validated, $cartItems, $products, $customer) {
            $order = Order::create([
                'session_id' => session()->getId(),
                'customer_id' => $customer?->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'status' => Order::STATUS_PENDING,
                'total_price' => 0,
                'payment_method' => $validated['payment_method'] ?? 'bank_transfer',
                'payment_status' => 'pending',
                'type' => 'normal',
            ]);

            $totalPrice = 0;

            foreach ($cartItems as $item) {
                $product = $products->get($item['id']);

                if (!$product) {
                    continue;
                }

                $quantity = (int) ($item['quantity'] ?? 1);

                $this->stockService->deduct($product, $quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => (float) $product->price,
                    'cost_price' => (float) ($product->cost_price ?? 0),
                ]);

                $totalPrice += (float) $product->price * $quantity;
            }

            $order->total_price = $totalPrice;
            $order->saveQuietly();

            $order->syncItemsFromOrderItems();

            $this->notificationService->newOrder($order);

            return $order;
        });

        return $order;
    }

    private function resolveCustomer(array $validated): ?Customer
    {
        $email = $validated['email'] ?? null;

        if (!$email) {
            return null;
        }

        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            $tempPassword = Str::random(12);
            $validated['_temp_password'] = $tempPassword;

            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $email,
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($tempPassword),
            ]);
        }

        return $customer;
    }

    private function sendWelcomeEmail(Customer $customer, ?string $tempPassword): void
    {
        if (!$tempPassword) return;

        try {
            Mail::raw(
                "Bonjour {$customer->name},\n\n"
                . "Un compte client a été créé automatiquement chez Optique Échouate.\n\n"
                . "Voici vos identifiants :\n"
                . "Email : {$customer->email}\n"
                . "Mot de passe temporaire : {$tempPassword}\n\n"
                . "Connectez-vous pour suivre vos commandes : " . route('login') . "\n\n"
                . "Merci de votre confiance !\n"
                . "L'équipe Optique Échouate",
                function ($message) use ($customer) {
                    $message->to($customer->email, $customer->name)
                        ->subject('Votre compte Optique Échouate');
                }
            );
        } catch (\Exception $e) {
            // Email is best-effort
        }
    }

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('orderItems.product');

            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $this->stockService->restore($item->product, $item->quantity);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });
    }

    public function restoreStock(Order $order): void
    {
        $order->load('orderItems.product');

        foreach ($order->orderItems as $item) {
            if ($item->product) {
                $this->stockService->restore($item->product, $item->quantity);
            }
        }
    }
}
