<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tables = ["orders", "order_items", "brands", "colors", "shapes", "genders", "customers", "quiz_answers", "settings", "stock_movements", "product_variants"];
foreach ($tables as $t) {
    echo "=== $t ===" . PHP_EOL;
    try {
        $rows = Illuminate\Support\Facades\DB::select("DESCRIBE $t");
        foreach ($rows as $r) {
            echo "  {$r->Field}: {$r->Type}" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "  ERROR: " . $e->getMessage() . PHP_EOL;
    }
}
