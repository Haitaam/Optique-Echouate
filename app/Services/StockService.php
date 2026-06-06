<?php

namespace App\Services;

use App\Features\Products\Models\Product;
use App\Models\Notification;
use RuntimeException;

class StockService
{
    public function deduct(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new RuntimeException(
                "Stock insuffisant pour « {$product->name} » : {$product->stock} disponible(s), {$quantity} demandé(s)."
            );
        }

        $product->decrement('stock', $quantity);

        $this->checkStockLevels($product);
    }

    public function restore(Product $product, int $quantity): void
    {
        $product->increment('stock', $quantity);
    }

    public function checkStockLevels(Product $product): void
    {
        if ($product->stock <= 0) {
            Notification::create([
                'type' => 'out_of_stock',
                'title' => 'Rupture de stock : ' . $product->name,
                'body' => 'Stock épuisé pour le produit #' . $product->id,
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
