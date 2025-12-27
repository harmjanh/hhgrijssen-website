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
        Schema::table('coin_orders', function (Blueprint $table) {
            $table->foreignId('pickup_moment_id')->nullable()->after('payment_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coin_orders', function (Blueprint $table) {
            $table->dropForeign(['pickup_moment_id']);
            $table->dropColumn('pickup_moment_id');
        });
    }
};
