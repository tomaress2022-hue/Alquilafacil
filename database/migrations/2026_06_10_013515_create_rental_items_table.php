<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla rental_items (ítems de cada alquiler).
     * 
     * Tabla PIVOTE con datos adicionales entre rentals y equipment.
     * Un alquiler puede incluir MÚLTIPLES equipos.
     * 
     * Ejemplo:
     *   Rental #5 → rental_item (equipo: Cámara, 3 días, $90)
     *             → rental_item (equipo: Trípode, 3 días, $15)
     *   total_price del rental = $105
     * 
     * Relaciones:
     * - rental_items PERTENECE A rentals (N:1)
     * - rental_items PERTENECE A equipment (N:1)
     */
    public function up(): void
    {
        Schema::create('rental_items', function (Blueprint $table) {
            // id: clave primaria auto-incremental
            $table->id();

            // rental_id: FK → referencia a rentals.id
            // cascade: si se elimina el alquiler, se eliminan sus ítems
            $table->foreignId('rental_id')
                  ->constrained('rentals')
                  ->onDelete('cascade');

            // equipment_id: FK → referencia a equipment.id
            // restrict: no se puede eliminar un equipo que tiene ítems
            $table->foreignId('equipment_id')
                  ->constrained('equipment')
                  ->onDelete('restrict');

            // daily_price: precio por día al momento del alquiler
            // Se guarda aquí porque el precio del equipo puede cambiar en el futuro
            // Esto preserva el precio histórico de la transacción
            $table->decimal('daily_price', 10, 2);

            // days: número de días de alquiler
            // Se calcula: end_date - start_date del rental padre
            $table->integer('days');

            // subtotal: precio total de este ítem
            // Fórmula: daily_price × days
            // También se guarda para eficiencia (evitar recalcular siempre)
            $table->decimal('subtotal', 10, 2);

            // timestamps: created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla rental_items al hacer rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_items');
    }
};