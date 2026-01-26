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
        Schema::table('users', function (Blueprint $table) {
            // Quitamos el nullable() haciendo que sea obligatoria
            $table->string('last_name')->nullable(false)->change();
            $table->string('username')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // En caso de error permite que vuelva a ser nullable
            $table->string('last_name')->nullable()->change();
            $table->string('username')->nullable()->change();
        });
    }
};
