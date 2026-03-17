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
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->boolean('coffee_needed')->default(false)->after('end_time');
            $table->boolean('has_break')->default(false)->after('coffee_needed');
            $table->boolean('beamer_needed')->default(false)->after('has_break');
            $table->boolean('guest_speaker')->default(false)->after('beamer_needed');
            $table->boolean('broadcast_needed')->default(false)->after('guest_speaker');
            $table->text('other_remarks')->nullable()->after('broadcast_needed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'coffee_needed',
                'has_break',
                'beamer_needed',
                'guest_speaker',
                'broadcast_needed',
                'other_remarks',
            ]);
        });
    }
};
