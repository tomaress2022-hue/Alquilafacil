<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración — crea la tabla users
     * Esta tabla almacena todos los usuarios del sistema:
     * administradores y clientes.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // id: clave primaria, auto-incremental (1, 2, 3...)
            $table->id();

            // name: nombre completo del usuario, máximo 255 caracteres
            $table->string('name');

            // email: correo único — no pueden existir dos iguales
            $table->string('email')->unique();

            // email_verified_at: fecha de verificación del correo (puede ser null)
            $table->timestamp('email_verified_at')->nullable();

            // password: contraseña encriptada con bcrypt
            $table->string('password');

            // role: define si es 'admin' o 'client'
            // default('client') → por defecto todos son clientes al registrarse
            $table->enum('role', ['admin', 'client'])->default('client');

            // documento: cédula o documento de identidad
            $table->string('documento', 20)->nullable();

            // phone: número de teléfono de contacto
            $table->string('phone', 20)->nullable();

            // remember_token: para la función "recuérdame" del login
            $table->rememberToken();

            // timestamps: crea automáticamente created_at y updated_at
            $table->timestamps();
        });

        // Tabla para sesiones de contraseña (viene con Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabla para sesiones del navegador (viene con Laravel)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Revierte la migración — elimina las tablas
     * Se ejecuta con: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};