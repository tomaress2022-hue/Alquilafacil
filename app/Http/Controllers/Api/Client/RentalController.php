<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /** GET /api/rentals — "Mis alquileres" del cliente autenticado */
    public function index(Request $request)
    {
        $query = $request->user()->rentals()->with('items.equipment');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->paginate(10);

        return RentalResource::collection($rentals);
    }

    /** POST /api/rentals — Crea una solicitud de alquiler */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date'      => 'required|date|after_or_equal:today',
            'end_date'        => 'required|date|after:start_date',
            'equipment_ids'   => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'notes'           => 'nullable|string|max:1000',
        ], [
            'equipment_ids.required' => 'Debes seleccionar al menos un equipo.',
            'end_date.after'         => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        $startDate = $validated['start_date'];
        $endDate   = $validated['end_date'];
        $days      = max(1, (new \DateTime($startDate))->diff(new \DateTime($endDate))->days);

        $equipmentItems = Equipment::whereIn('id', $validated['equipment_ids'])->get();

        foreach ($equipmentItems as $eq) {
            if (!$eq->isAvailable()) {
                return response()->json([
                    'message' => "El equipo '{$eq->name}' ya no está disponible.",
                ], 422);
            }
        }

        $rental = DB::transaction(function () use ($request, $validated, $startDate, $endDate, $days, $equipmentItems) {
            $rental = Rental::create([
                'client_id'   => $request->user()->id,
                'status'      => Rental::STATUS_PENDING,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'total_price' => 0,
                'notes'       => $validated['notes'] ?? null,
            ]);

            foreach ($equipmentItems as $eq) {
                RentalItem::createForRental($rental->id, $eq->id, (float) $eq->daily_price, $days);
            }

            $rental->recalculateTotal();

            return $rental;
        });

        return (new RentalResource($rental->load('items.equipment')))
            ->response()
            ->setStatusCode(201);
    }

    /** GET /api/rentals/{rental} — Detalle (solo si es dueño) */
    public function show(Request $request, Rental $rental)
    {
        if ($rental->client_id !== $request->user()->id) {
            abort(403);
        }

        $rental->load('items.equipment.category');

        return new RentalResource($rental);
    }

    /** PATCH /api/rentals/{rental}/cancel — Cancela un alquiler pendiente */
    public function cancel(Request $request, Rental $rental)
    {
        if ($rental->client_id !== $request->user()->id) {
            abort(403);
        }

        if (!$rental->canBeCancelled()) {
            return response()->json([
                'message' => 'Solo se pueden cancelar alquileres pendientes.',
            ], 422);
        }

        $rental->update(['status' => Rental::STATUS_CANCELLED]);

        return new RentalResource($rental->load('items.equipment'));
    }
}
