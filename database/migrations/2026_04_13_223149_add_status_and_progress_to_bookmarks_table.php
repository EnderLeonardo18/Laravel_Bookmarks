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
        Schema::table('bookmarks', function (Blueprint $table) {
            // 'status' lo hacemos obligatorio pero con un valor por defecto
            $table->string('status')->default('Pendiente')->after('category');

            // 'progress_note' es el texto manual (opcional)
            $table->string('progress_note')->nullable()->after('status');

            // 'progress_url' es el link específico (opcional)
            $table->text('progress_url')->nullable()->after('progress_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropColumn(['status', 'progress_note', 'progress_url']);
        });
    }
};
