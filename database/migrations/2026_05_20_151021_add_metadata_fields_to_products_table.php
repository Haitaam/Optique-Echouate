<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('style_tags')->nullable()->after('is_featured');
            $table->string('secondary_color', 50)->nullable()->after('color');
            $table->boolean('is_luxury')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['style_tags', 'secondary_color', 'is_luxury']);
        });
    }
};
