<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'equipment_id',
        'daily_price',
        'days',
        'subtotal',
    ];

    protected $casts = [
        'daily_price' => 'decimal:2',
        'subtotal'    => 'decimal:2',
    ];

    // =====================================================
    // RELACIONES ELOQUENT
    // =====================================================

    /**
     * Un ítem PERTENECE A un alquiler
     * Uso: $item->rental → objeto Rental
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Un ítem PERTENECE A un equipo
     * Uso: $item->equipment → objeto Equipment
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    // =====================================================
    // MÉTODO ESTÁTICO — Crea ítem con cálculo automático
    // =====================================================

    /**
     * Crea un ítem de alquiler calculando automáticamente el subtotal
     * 
     * @param int   $rentalId    ID del alquiler padre
     * @param int   $equipmentId ID del equipo
     * @param float $dailyPrice  Precio diario actual del equipo
     * @param int   $days        Número de días
     */
    public static function createForRental(
        int $rentalId,
        int $equipmentId,
        float $dailyPrice,
        int $days
    ): self {
        return self::create([
            'rental_id'    => $rentalId,
            'equipment_id' => $equipmentId,
            'daily_price'  => $dailyPrice,
            'days'         => $days,
            'subtotal'     => $dailyPrice * $days, // Fórmula del negocio
        ]);
    }
}