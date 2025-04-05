<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('street');
            $table->string('number');
            $table->string('zipcode');
            $table->string('city');
            $table->text('bankaccountnumber'); // Will be encrypted
            $table->decimal('amount', 10, 2);
            $table->text('explanation');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('declaration_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->integer('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaration_attachments');
        Schema::dropIfExists('declarations');
    }
};
