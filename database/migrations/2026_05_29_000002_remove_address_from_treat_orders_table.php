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
        Schema::table('treat_orders', function (Blueprint $table) {
            $table->dropColumn(['street', 'number', 'zipcode', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treat_orders', function (Blueprint $table) {
            $table->string('street')->default('');
            $table->string('number')->default('');
            $table->string('zipcode')->default('');
            $table->string('city')->default('');
        });
    }
};
