<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('brand');
            $table->string('color');
            $table->string('frame_shape');
            $table->string('gender');
            $table->string('material');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->index('brand');
            $table->index('color');
            $table->index('frame_shape');
            $table->index('gender');
            $table->index('material');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
