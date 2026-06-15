<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'status',
        'start_date',
        'end_date',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'total_price' => 'decimal:2',
    ];

    // =====================================================
    // CONSTANTES DE ESTADO
    // =====================================================

    const STATUS_PENDING   = 'pending';
    const STATUS_ACTIVE    = 'active';
    const STATUS_RETURNED  = 'returned';
    const STATUS_CANCELLED = 'cancelled';

    // =====================================================
    // RELACIONES ELOQUENT
    // =====================================================

    /**
     * Un alquiler PERTENECE A un usuario (cliente)
     * Tipo: belongsTo (N:1)
     * 
     * Uso: $rental->client → objeto User del cliente
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Un alquiler TIENE MUCHOS ítems
     * Tipo: hasMany (1:N)
     * 
     * Uso: $rental->items → colección de RentalItem
     */
    public function items()
    {
        return $this->hasMany(RentalItem::class);
    }

    // =====================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================

    /**
     * Calcula los días entre start_date y end_date
     * Usa Carbon (librería de fechas incluida en Laravel)
     */
    public function calculateDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Recalcula y guarda el total basado en los ítems actuales
     */
    public function recalculateTotal(): void
    {
        $total = $this->items()->sum('subtotal');
        $this->update(['total_price' => $total]);
    }

    /**
     * Verifica si el alquiler puede ser cancelado
     * Solo los alquileres 'pending' pueden cancelarse
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Badge Bootstrap según estado del alquiler
     */
    public function statusBadge(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'active'    => '<span class="badge bg-success">Activo</span>',
            'returned'  => '<span class="badge bg-secondary">Devuelto</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
            default     => '<span class="badge bg-light text-dark">Desconocido</span>',
        };
    }

    /**
     * Nombre en español del estado
     */
    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'Pendiente',
            'active'    => 'Activo',
            'returned'  => 'Devuelto',
            'cancelled' => 'Cancelado',
            default     => 'Desconocido',
        };
    }
}