<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add stock management fields
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('image');
            }
            if (!Schema::hasColumn('products', 'min_stock_threshold')) {
                $table->integer('min_stock_threshold')->default(3)->after('stock');
            }
        });

        // Add foreign key columns and migrate data from string fields
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete()->after('brand');
            }
            if (!Schema::hasColumn('products', 'color_id')) {
                $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete()->after('color');
            }
            if (!Schema::hasColumn('products', 'shape_id')) {
                $table->foreignId('shape_id')->nullable()->constrained('shapes')->nullOnDelete()->after('frame_shape');
            }
            if (!Schema::hasColumn('products', 'gender_id')) {
                $table->foreignId('gender_id')->nullable()->constrained()->nullOnDelete()->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['color_id']);
            $table->dropForeign(['shape_id']);
            $table->dropForeign(['gender_id']);
            $table->dropColumn(['brand_id', 'color_id', 'shape_id', 'gender_id', 'cost_price', 'stock', 'min_stock_threshold']);
        });
    }
};
