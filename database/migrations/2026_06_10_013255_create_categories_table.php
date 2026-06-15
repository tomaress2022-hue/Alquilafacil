<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla categories.
     * 
     * Las categorías agrupan los equipos por tipo:
     * Ejemplo: "Informática", "Sonido", "Video", "Herramientas"
     * 
     * Relación: Una categoría TIENE MUCHOS equipos (1:N)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // id: clave primaria auto-incremental
            $table->id();

            // name: nombre de la categoría (ej: "Equipos de Sonido")
            // unique() garantiza que no haya dos categorías con el mismo nombre
            $table->string('name')->unique();

            // description: descripción opcional de la categoría
            // nullable() → puede estar vacío
            $table->text('description')->nullable();

            // timestamps: created_at y updated_at automáticos
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla categories al hacer rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};