<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->nullable();
            $table->string('image')->nullable();
            $table->string('name');
            $table->string('quantity');
            $table->decimal('starting_price', 10, 2);
            $table->enum('status', ['done', 'cancel', 'waiting']);
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->timestamp('end_time')->nullable(); // لحفظ وقت انتهاء المزاد
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
