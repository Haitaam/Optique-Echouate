<?php

namespace Tests\Feature;

use App\Features\Products\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creates_order_items(): void
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 10,
        ]);

        $orderService = $this->app->make(OrderService::class);

        $order = $orderService->createFromCheckout([
            'name' => 'Test Client',
            'phone' => '0612345678',
            'email' => 'test@example.com',
            'city' => 'Casablanca',
            'address' => '123 Test St',
            'notes' => null,
            'payment_method' => 'bank_transfer',
        ], [
            ['id' => $product->id, 'quantity' => 2],
        ]);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);
        $this->assertEquals(200.00, $order->fresh()->total_price);
    }

    public function test_stock_deducted_on_order(): void
    {
        $product = Product::factory()->create([
            'price' => 50.00,
            'stock' => 5,
        ]);

        $orderService = $this->app->make(OrderService::class);

        $orderService->createFromCheckout([
            'name' => 'Stock Test',
            'phone' => '0612345678',
            'email' => 'stock@test.com',
            'city' => 'Rabat',
            'address' => '456 Test Ave',
            'payment_method' => 'bank_transfer',
        ], [
            ['id' => $product->id, 'quantity' => 3],
        ]);

        $this->assertEquals(2, $product->fresh()->stock);
    }

    public function test_oversell_prevented(): void
    {
        $this->expectException(\RuntimeException::class);

        $product = Product::factory()->create([
            'price' => 50.00,
            'stock' => 2,
        ]);

        $orderService = $this->app->make(OrderService::class);

        $orderService->createFromCheckout([
            'name' => 'Oversell Test',
            'phone' => '0612345678',
            'email' => 'oversell@test.com',
            'city' => 'Fes',
            'address' => '789 Test Blvd',
            'payment_method' => 'bank_transfer',
        ], [
            ['id' => $product->id, 'quantity' => 5],
        ]);
    }

    public function test_stock_restored_on_cancel(): void
    {
        $product = Product::factory()->create([
            'price' => 75.00,
            'stock' => 10,
        ]);

        $orderService = $this->app->make(OrderService::class);

        $order = $orderService->createFromCheckout([
            'name' => 'Cancel Test',
            'phone' => '0612345678',
            'email' => 'cancel@test.com',
            'city' => 'Marrakech',
            'address' => '321 Cancel Rd',
            'payment_method' => 'bank_transfer',
        ], [
            ['id' => $product->id, 'quantity' => 4],
        ]);

        $this->assertEquals(6, $product->fresh()->stock);

        $orderService->cancelOrder($order);

        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_order_has_correct_status_flow(): void
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $this->assertTrue($order->canTransitionTo(Order::STATUS_CONFIRMED));
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CANCELLED));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_SHIPPED));

        $order->update(['status' => Order::STATUS_CONFIRMED]);
        $this->assertTrue($order->fresh()->canTransitionTo(Order::STATUS_PREPARING));
        $this->assertTrue($order->fresh()->canTransitionTo(Order::STATUS_CANCELLED));

        $order->update(['status' => Order::STATUS_DELIVERED]);
        $this->assertFalse($order->fresh()->canTransitionTo(Order::STATUS_CANCELLED));
    }

    public function test_admin_access_requires_auth(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }
}
