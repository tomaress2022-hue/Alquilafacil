<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla rentals (alquileres/solicitudes).
     * 
     * Cada alquiler es creado por un cliente (client_id).
     * El admin aprueba, activa o registra la devolución.
     * 
     * Relaciones:
     * - rentals PERTENECE A users (cliente) (N:1)
     * - rentals TIENE MUCHOS rental_items (1:N)
     * 
     * Ciclo de vida del status:
     * pending → active → returned
     *         ↘ cancelled
     */
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            // id: clave primaria auto-incremental
            $table->id();

            // client_id: CLAVE FORÁNEA → referencia a users.id
            // El cliente que solicita el alquiler
            // onDelete('restrict') → no se puede borrar un usuario con alquileres
            $table->foreignId('client_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            // status: estado del alquiler
            // 'pending'   → solicitud creada, esperando aprobación del admin
            // 'active'    → aprobado por admin, equipo en uso
            // 'returned'  → equipo devuelto, proceso completado
            // 'cancelled' → cancelado por cliente o admin
            $table->enum('status', ['pending', 'active', 'returned', 'cancelled'])
                  ->default('pending');

            // start_date: fecha de inicio del alquiler
            $table->date('start_date');

            // end_date: fecha de fin del alquiler
            $table->date('end_date');

            // total_price: precio total calculado (suma de subtotales de ítems)
            // Se calcula al crear la solicitud: SUM(daily_price × days) de cada ítem
            $table->decimal('total_price', 10, 2)->default(0);

            // notes: observaciones opcionales del cliente o admin
            $table->text('notes')->nullable();

            // timestamps: created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla rentals al hacer rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};