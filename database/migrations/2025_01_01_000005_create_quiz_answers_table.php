<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('glasses_type')->nullable();
            $table->string('style')->nullable();
            $table->string('shape')->nullable();
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('lifestyle')->nullable();
            $table->timestamps();
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
