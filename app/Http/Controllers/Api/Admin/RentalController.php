<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /** GET /api/admin/rentals */
    public function index(Request $request)
    {
        $query = Rental::with(['client', 'items.equipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->paginate(15);

        return RentalResource::collection($rentals);
    }

    /** GET /api/admin/rentals/{rental} */
    public function show(Rental $rental)
    {
        $rental->load(['client', 'items.equipment.category']);

        return new RentalResource($rental);
    }

    /**
     * PATCH /api/admin/rentals/{rental}/approve
     * status → 'active', equipos del alquiler → 'rented'
     */
    public function approve(Rental $rental)
    {
        if ($rental->status !== 'pending') {
            return response()->json([
                'message' => 'Solo se pueden aprobar solicitudes en estado pendiente.',
            ], 422);
        }

        foreach ($rental->items as $item) {
            if ($item->equipment->status !== 'available') {
                return response()->json([
                    'message' => "El equipo '{$item->equipment->name}' ya no está disponible.",
                ], 422);
            }
        }

        DB::transaction(function () use ($rental) {
            $rental->update(['status' => 'active']);

            foreach ($rental->items as $item) {
                $item->equipment->update(['status' => 'rented']);
            }
        });

        return new RentalResource($rental->load(['client', 'items.equipment']));
    }

    /**
     * PATCH /api/admin/rentals/{rental}/return
     * status → 'returned', equipos del alquiler → 'available'
     */
    public function returnRental(Rental $rental)
    {
        if ($rental->status !== 'active') {
            return response()->json([
                'message' => 'Solo se puede registrar devolución de alquileres activos.',
            ], 422);
        }

        DB::transaction(function () use ($rental) {
            $rental->update(['status' => 'returned']);

            foreach ($rental->items as $item) {
                $item->equipment->update(['status' => 'available']);
            }
        });

        return new RentalResource($rental->load(['client', 'items.equipment']));
    }
}
