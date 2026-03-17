<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->boolean('coffee_needed')->nullable()->default(null)->change();
            $table->boolean('has_break')->nullable()->default(null)->change();
            $table->boolean('beamer_needed')->nullable()->default(null)->change();
            $table->boolean('guest_speaker')->nullable()->default(null)->change();
            $table->boolean('broadcast_needed')->nullable()->default(null)->change();
        });

        DB::table('room_reservations')
            ->whereNull('other_remarks')
            ->update([
                'coffee_needed' => null,
                'has_break' => null,
                'beamer_needed' => null,
                'guest_speaker' => null,
                'broadcast_needed' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('room_reservations')
            ->whereNull('coffee_needed')
            ->orWhereNull('has_break')
            ->orWhereNull('beamer_needed')
            ->orWhereNull('guest_speaker')
            ->orWhereNull('broadcast_needed')
            ->update([
                'coffee_needed' => false,
                'has_break' => false,
                'beamer_needed' => false,
                'guest_speaker' => false,
                'broadcast_needed' => false,
            ]);

        Schema::table('room_reservations', function (Blueprint $table) {
            $table->boolean('coffee_needed')->nullable(false)->default(false)->change();
            $table->boolean('has_break')->nullable(false)->default(false)->change();
            $table->boolean('beamer_needed')->nullable(false)->default(false)->change();
            $table->boolean('guest_speaker')->nullable(false)->default(false)->change();
            $table->boolean('broadcast_needed')->nullable(false)->default(false)->change();
        });
    }
};
