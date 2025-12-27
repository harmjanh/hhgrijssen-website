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
        Schema::create('privacy_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('street');
            $table->string('zipcode');
            $table->string('city');
            $table->date('birth_date');
            $table->boolean('voorbede_eredienst')->default(false);
            $table->boolean('voorbede_zaaier')->default(false);
            $table->boolean('verjaardag_zaaier')->default(false);
            $table->boolean('rsv_gegevens')->default(false);
            $table->string('place');
            $table->date('submission_date');
            $table->boolean('agreed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_consents');
    }
};
