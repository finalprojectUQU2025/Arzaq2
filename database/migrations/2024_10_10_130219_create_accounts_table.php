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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['mazarie', 'tajir']);
            $table->string('image')->nullable();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('check_conditions')->default(false);
            $table->enum('status', ['verefy', 'unVerefy', 'blocked'])->default('unVerefy');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('forgetPassword_code')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
