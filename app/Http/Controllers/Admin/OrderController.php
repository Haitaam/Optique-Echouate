<?php

namespace App\Http\Controllers\Admin;

use App\Mail\OrderStatusChanged;
use App\Features\Products\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class OrderController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $allowedSorts = ['id', 'name', 'total_price', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'created_at';
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';

        $query = Order::orderBy($sortBy, $sortOrder);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20)->withQueryString();
        $pendingCount = Order::pendingConfirmation()->count();

        return view('admin.orders.index', compact('orders', 'pendingCount', 'sortBy', 'sortOrder'));
    }

    public function pending()
    {
        $orders = Order::pendingConfirmation()->latest()->paginate(20);
        $pendingCount = $orders->total();

        return view('admin.orders.pending', compact('orders', 'pendingCount'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(Order::STATUSES)],
            'payment_status' => 'nullable|string|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['status']) && $validated['status'] !== $order->status) {
            if (!$order->canTransitionTo($validated['status'])) {
                return redirect()->route('admin.orders.index')
                    ->with('error', 'Transition de statut invalide.');
            }

            if ($validated['status'] === Order::STATUS_CANCELLED) {
                $this->restoreStock($order);
            }
        }

        $oldStatus = $order->status;
        $order->update($validated);

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            try {
                Mail::to($order->email, $order->name)->send(new OrderStatusChanged($order));
            } catch (\Exception $e) {
                // Email is best-effort
            }
        }

        return redirect()->route('admin.orders.index')
            ->with('success', 'Commande mise à jour avec succès.');
    }

    public function confirm(Order $order)
    {
        if (!$order->canTransitionTo(Order::STATUS_CONFIRMED)) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cette commande ne peut pas être confirmée.');
        }

        $order->update(['status' => Order::STATUS_CONFIRMED]);

        try {
            Mail::to($order->email, $order->name)->send(new OrderStatusChanged($order));
        } catch (\Exception $e) {
            // Email is best-effort
        }

        return redirect()->route('admin.orders.pending')
            ->with('success', 'Commande #' . $order->id . ' confirmée avec succès.');
    }

    public function cancel(Order $order)
    {
        if (!$order->canTransitionTo(Order::STATUS_CANCELLED)) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'Cette commande ne peut pas être annulée.');
        }

        $this->restoreStock($order);

        $order->update(['status' => Order::STATUS_CANCELLED]);

        try {
            Mail::to($order->email, $order->name)->send(new OrderStatusChanged($order));
        } catch (\Exception $e) {
            // Email is best-effort
        }

        return redirect()->route('admin.orders.index')
            ->with('success', 'Commande #' . $order->id . ' annulée.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Order::STATUSES)],
        ]);

        if (!$order->canTransitionTo($validated['status'])) {
            return back()->with('error', 'Transition de statut invalide.');
        }

        if ($validated['status'] === Order::STATUS_CANCELLED) {
            $this->restoreStock($order);
        }

        $order->update(['status' => $validated['status']]);

        try {
            Mail::to($order->email, $order->name)->send(new OrderStatusChanged($order));
        } catch (\Exception $e) {
            // Email is best-effort
        }

        return back()->with('success', 'Statut de la commande #' . $order->id . ' mis à jour.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Commande supprimée avec succès.');
    }

    public function sendWhatsApp(Order $order)
    {
        $order->update(['whatsapp_sent' => true]);

        return redirect()->back()->with('success', 'WhatsApp marqué comme envoyé.');
    }

    private function restoreStock(Order $order): void
    {
        $items = $order->items;
        if (!is_array($items)) return;

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? $item['id'] ?? null;
            $quantity = (int) ($item['quantity'] ?? 1);

            if ($productId) {
                Product::where('id', $productId)->increment('stock', $quantity);
            }
        }
    }
}
