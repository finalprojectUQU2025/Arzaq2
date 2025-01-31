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
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('country_id')->after('email')->nullable()->constrained()->onDelete('cascade'); // تعديل هنا
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['country_id']); // لحذف العلاقة الخارجية
            $table->dropColumn('country_id'); // لحذف العمود عند التراجع
        });
    }
};
