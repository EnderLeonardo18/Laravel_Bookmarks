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
        Schema::table('users', function(Blueprint $table){
            // 1. Renombramos primero
            $table->renameColumn('name', 'first_name');

            // 2. Agregamos usando el NUEVO nombre como referencia
            // el after: Le dice a MySQL que coloque esta columna justo después
            $table->string('last_name')->after('first_name')->nullable();
            // unique(): No puede existir dos username en la BD
            $table->string('username')->nullable()->unique()->after('email');
        });

    }

    /**
     * Reverse the migrations.
     * El método 'down' se ejecuta al usar 'php artisan migrate:rollback'.
     * Sirve para deshacer exactamente lo que hizo el método 'up'.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table){
            // Revierte el nombre: de 'first_name' vuelve a ser 'name'
            $table->renameColumn('first_name', 'name');
            $table->dropColumn(['last_name', 'username']);
        });
    }
};
