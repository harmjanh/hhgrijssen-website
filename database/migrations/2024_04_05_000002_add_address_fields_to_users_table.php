<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('phonenumber')->nullable();
            $table->text('bankaccountnumber')->nullable(); // Will be encrypted
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'street',
                'number',
                'zipcode',
                'city',
                'phonenumber',
                'bankaccountnumber',
            ]);
        });
    }
};
