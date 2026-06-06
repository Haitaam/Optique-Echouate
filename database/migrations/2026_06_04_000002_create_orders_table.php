<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('city');
            $table->text('address');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending_confirmation');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable()->default('pending');
            $table->string('type')->nullable()->default('normal');
            $table->boolean('whatsapp_sent')->default(false);
            $table->text('return_reason')->nullable();
            $table->string('prescription_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
