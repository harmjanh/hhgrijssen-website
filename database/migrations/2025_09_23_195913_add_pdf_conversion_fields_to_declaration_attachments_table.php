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
        Schema::table('declaration_attachments', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('path');
            $table->boolean('is_pdf_converted')->default(false)->after('pdf_path');
            $table->timestamp('pdf_converted_at')->nullable()->after('is_pdf_converted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declaration_attachments', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'is_pdf_converted', 'pdf_converted_at']);
        });
    }
};
