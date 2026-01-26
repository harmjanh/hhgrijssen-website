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
        Schema::create('scipio_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('regnr')->unique();
            $table->string('phonenumber')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('has_solidarity_fund')->default(false);
            $table->boolean('has_zaaier')->default(false);
            $table->boolean('has_privacy_consent')->default(false);
            $table->boolean('has_vwb')->default(false);
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->index('regnr');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scipio_registrations');
    }
};
