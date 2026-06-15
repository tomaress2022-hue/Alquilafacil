<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla equipment (equipos).
     * 
     * Cada equipo pertenece a una categoría (FK: category_id).
     * El campo 'status' controla la disponibilidad del equipo.
     * 
     * Relaciones:
     * - equipment PERTENECE A categories (N:1)
     * - equipment TIENE MUCHOS rental_items (1:N)
     */
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            // id: clave primaria auto-incremental
            $table->id();

            // category_id: CLAVE FORÁNEA que referencia a categories.id
            // constrained() busca automáticamente la tabla 'categories'
            // onDelete('cascade') → si se elimina la categoría, se eliminan sus equipos
            // NOTA: En producción usar onDelete('restrict') para evitar pérdida de datos
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('restrict'); // No permite borrar categoría con equipos

            // name: nombre descriptivo del equipo
            // Ejemplo: "MacBook Pro 14 M3", "Canon EOS R5"
            $table->string('name');

            // code: código único interno para identificar el equipo físicamente
            // Ejemplo: "CAM-001", "COMP-003", "SND-012"
            $table->string('code', 50)->unique();

            // description: descripción detallada del equipo
            $table->text('description')->nullable();

            // daily_price: precio por día de alquiler
            // decimal(10, 2) → hasta 9,999,999.99 (10 dígitos, 2 decimales)
            $table->decimal('daily_price', 10, 2);

            // status: estado actual del equipo
            // 'available'   → disponible para alquilar
            // 'rented'      → actualmente en alquiler
            // 'maintenance' → en reparación o mantenimiento
            $table->enum('status', ['available', 'rented', 'maintenance'])
                  ->default('available');

            // image: ruta de la imagen guardada en storage/app/public
            // Ejemplo: "equipment/camara-001.jpg"
            $table->string('image')->nullable();

            // timestamps: created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla equipment al hacer rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};