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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade')->nullable();
            $table->decimal('balance', 15, 2)->default(0.00); // الرصيد الحالي
            // معلومات البطاقة
            $table->string('card_holder_name')->nullable(); // اسم صاحب البطاقة
            $table->string('card_number')->nullable(); // رقم البطاقة (PAN)
            $table->string('card_expiry')->nullable(); // تاريخ انتهاء البطاقة (MM/YY)
            $table->string('card_cvv')->nullable(); // رمز CVV للبطاقة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
