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
        Schema::table('declarations', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->date('date_of_service')->nullable()->after('email');
            $table->time('time_of_service_1')->nullable()->after('date_of_service');
            $table->time('time_of_service_2')->nullable()->after('time_of_service_1');
            $table->integer('kilometers')->default(0)->after('time_of_service_2');
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declarations', function (Blueprint $table) {
            $table->dropColumn(['email', 'date_of_service', 'time_of_service_1', 'time_of_service_2', 'kilometers']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
