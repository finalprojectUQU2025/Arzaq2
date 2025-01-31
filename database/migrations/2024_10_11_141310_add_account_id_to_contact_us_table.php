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
        Schema::table('contact_us', function (Blueprint $table) {
            $table->foreignId('account_id')->after('details')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->dropForeign(['account_id']); // حذف المفتاح الخارجي
            $table->dropColumn('account_id'); // حذف العمود
        });
    }
};
