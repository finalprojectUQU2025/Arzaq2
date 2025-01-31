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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // معرف الإشعار
            $table->string('title'); // عنوان الإشعار
            $table->text('details'); // تفاصيل الإشعار
            $table->foreignId('account_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false); // هل تم قراءة الإشعار
            $table->boolean('is_for_all')->default(false); // هل الإشعار لجميع المستخدمين
            $table->enum('status', ['mazarie', 'tajir', 'all'])->default('all');

            $table->string('name_moza')->nullable();
            $table->string('image_moza')->nullable();
            $table->string('city_moza')->nullable();
            $table->timestamps(); // أوقات الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
