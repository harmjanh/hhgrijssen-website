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
        // Skip dropping the constraint for now - we'll handle time conflicts in the application logic
        // The old constraint will be replaced by application-level validation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            // Restore the old unique constraint
            $table->unique(['room_id', 'start_time']);
        });
    }
};
