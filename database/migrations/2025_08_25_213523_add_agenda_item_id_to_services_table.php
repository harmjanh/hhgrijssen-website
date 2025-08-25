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
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('agenda_item_id')->constrained()->onDelete('cascade');
            $table->dropColumn('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dateTime('start_date');
            $table->dropForeign(['agenda_item_id']);
            $table->dropColumn('agenda_item_id');
        });
    }
};
