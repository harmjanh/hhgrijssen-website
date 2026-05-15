<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('contact_form_enabled')->default(false)->after('is_published');
            $table->string('contact_form_recipient')->nullable()->after('contact_form_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['contact_form_enabled', 'contact_form_recipient']);
        });
    }
};
