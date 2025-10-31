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
            $table->renameColumn('blue_coins', 'silver_coins');
            $table->renameColumn('red_coins', 'gold_coins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coin_orders', function (Blueprint $table) {
            $table->renameColumn('silver_coins', 'blue_coins');
            $table->renameColumn('gold_coins', 'red_coins');
        });
    }
};
