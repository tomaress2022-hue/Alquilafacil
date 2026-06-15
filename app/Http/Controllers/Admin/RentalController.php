<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /** Lista todos los alquileres */
    public function index(Request $request)
    {
        $query = Rental::with(['client', 'items.equipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->paginate(15);
        return view('admin.rentals.index', compact('rentals'));
    }

    /** Ver detalle de un alquiler */
    public function show(Rental $rental)
    {
        $rental->load(['client', 'items.equipment.category']);
        return view('admin.rentals.show', compact('rental'));
    }

    /**
     * APROBAR solicitud de alquiler
     * Lógica: status → 'active', todos los equipos → 'rented'
     */
    public function approve(Rental $rental)
    {
        // Solo se pueden aprobar solicitudes pendientes
        if ($rental->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Solo se pueden aprobar solicitudes en estado pendiente.');
        }

        // Verificar que todos los equipos sigan disponibles
        foreach ($rental->items as $item) {
            if ($item->equipment->status !== 'available') {
                return redirect()->back()->with('error',
                    "El equipo '{$item->equipment->name}' ya no está disponible."
                );
            }
        }

        // Usar transacción para garantizar consistencia
        // Si algo falla a mitad, todo se revierte
        DB::transaction(function () use ($rental) {
            // Cambiar estado del alquiler a activo
            $rental->update(['status' => 'active']);

            // Marcar TODOS los equipos del alquiler como 'rented'
            foreach ($rental->items as $item) {
                $item->equipment->update(['status' => 'rented']);
            }
        });

        return redirect()->back()
            ->with('success', '✅ Alquiler aprobado. Los equipos fueron marcados como "En alquiler".');
    }

    /**
     * REGISTRAR DEVOLUCIÓN del alquiler
     * Lógica: status → 'returned', todos los equipos → 'available'
     */
    public function returnRental(Rental $rental)
    {
        // Solo alquileres activos pueden registrar devolución
        if ($rental->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Solo se puede registrar devolución de alquileres activos.');
        }

        DB::transaction(function () use ($rental) {
            // Cambiar estado del alquiler a devuelto
            $rental->update(['status' => 'returned']);

            // Liberar TODOS los equipos → 'available'
            foreach ($rental->items as $item) {
                $item->equipment->update(['status' => 'available']);
            }
        });

        return redirect()->back()
            ->with('success', '📦 Devolución registrada. Los equipos están disponibles nuevamente.');
    }
}