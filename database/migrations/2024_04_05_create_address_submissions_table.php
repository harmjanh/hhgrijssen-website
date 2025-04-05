<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('date_of_birth');
            $table->string('phone_number');

            $table->string('old_street');
            $table->string('old_number');
            $table->string('old_zipcode');
            $table->string('old_city');

            $table->string('new_street');
            $table->string('new_number');
            $table->string('new_zipcode');
            $table->string('new_city');

            $table->text('other_people')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_submissions');
    }
};
