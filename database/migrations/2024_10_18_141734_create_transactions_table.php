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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // ربط الحركة بالمحفظة
            $table->enum('type', ['deposit', 'withdrawal']); // نوع الحركة (إيداع أو سحب)
            $table->decimal('amount', 10, 2); // مبلغ الحركة
            $table->string('description')->nullable(); // وصف اختياري للحركة
            $table->timestamps(); // وقت الحركة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
