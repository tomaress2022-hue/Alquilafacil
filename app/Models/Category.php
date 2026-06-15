<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Campos asignables masivamente
     */
    protected $fillable = [
        'name',
        'description',
    ];

    // =====================================================
    // RELACIONES ELOQUENT
    // =====================================================

    /**
     * Una categoría TIENE MUCHOS equipos
     * Tipo: hasMany (1:N)
     * 
     * Uso: $category->equipment → colección de equipos de la categoría
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Cuenta los equipos disponibles en esta categoría
     * Uso: $category->availableEquipmentCount()
     */
    public function availableEquipmentCount(): int
    {
        return $this->equipment()->where('status', 'available')->count();
    }
}