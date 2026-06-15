<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    /**
     * Nombre explícito de la tabla (Laravel inferiría 'equipments' por defecto)
     */
    protected $table = 'equipment';

    /**
     * Campos asignables masivamente
     */
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'description',
        'daily_price',
        'status',
        'image',
    ];

    /**
     * Conversiones de tipo automáticas
     */
    protected $casts = [
        'daily_price' => 'decimal:2',
    ];

    // =====================================================
    // CONSTANTES DE ESTADO — Evita errores de typo
    // =====================================================

    const STATUS_AVAILABLE   = 'available';
    const STATUS_RENTED      = 'rented';
    const STATUS_MAINTENANCE = 'maintenance';

    // =====================================================
    // RELACIONES ELOQUENT
    // =====================================================

    /**
     * Un equipo PERTENECE A una categoría
     * Tipo: belongsTo (N:1)
     * 
     * Uso: $equipment->category → objeto Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Un equipo TIENE MUCHOS ítems de alquiler (histórico)
     * Tipo: hasMany (1:N)
     * 
     * Uso: $equipment->rentalItems → colección de RentalItem
     */
    public function rentalItems()
    {
        return $this->hasMany(RentalItem::class);
    }

    // =====================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================

    /**
     * Verifica si el equipo está disponible para alquilar
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Retorna el badge HTML de Bootstrap según el estado
     */
    public function statusBadge(): string
    {
        return match($this->status) {
            'available'   => '<span class="badge bg-success">Disponible</span>',
            'rented'      => '<span class="badge bg-warning text-dark">En alquiler</span>',
            'maintenance' => '<span class="badge bg-danger">Mantenimiento</span>',
            default       => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    /**
     * URL de la imagen del equipo
     * Si no tiene imagen, retorna un placeholder
     */
    public function imageUrl(): string
    {
        if ($this->image && file_exists(storage_path('app/public/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        // Placeholder en SVG embebido (no depende de servicios externos)
        $label = rawurlencode($this->name ?? 'Sin imagen');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300">'
             . '<rect width="100%" height="100%" fill="%23e2e8f0"/>'
             . '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" '
             . 'font-family="sans-serif" font-size="18" fill="%2364748b">' . $label . '</text>'
             . '</svg>';

        return 'data:image/svg+xml,' . $svg;
    }
}