<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /** Dashboard del cliente: resumen de sus alquileres */
    public function dashboard()
    {
        $user = auth()->user();

        $rentals = $user->rentals()
            ->with('items.equipment')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'pending'  => $user->rentals()->where('status', 'pending')->count(),
            'active'   => $user->rentals()->where('status', 'active')->count(),
            'returned' => $user->rentals()->where('status', 'returned')->count(),
            'total'    => $user->rentals()->count(),
        ];

        return view('client.dashboard', compact('rentals', 'stats'));
    }

    /** Catálogo de equipos con filtros */
    public function catalog(Request $request)
    {
        $query = Equipment::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $equipment  = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('client.catalog', compact('equipment', 'categories'));
    }

    /** Detalle de un equipo */
    public function showEquipment(Equipment $equipment)
    {
        $equipment->load('category');
        return view('client.equipment-detail', compact('equipment'));
    }

    /** Listado de "Mis Alquileres" del cliente */
    public function myRentals(Request $request)
    {
        $query = auth()->user()->rentals()->with('items.equipment');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest()->paginate(10);

        return view('client.my-rentals', compact('rentals'));
    }

    /** Formulario para crear una solicitud de alquiler */
    public function createRental(Request $request)
    {
        $availableEquipment = Equipment::where('status', Equipment::STATUS_AVAILABLE)
            ->with('category')
            ->get();

        $selectedEquipment = null;
        if ($request->filled('equipment_id')) {
            $selectedEquipment = $availableEquipment->firstWhere('id', (int) $request->equipment_id);
        }

        return view('client.create-rental', compact('availableEquipment', 'selectedEquipment'));
    }

    /** Guarda la solicitud de alquiler */
    public function storeRental(Request $request)
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

        // Calcular días entre fechas (mínimo 1)
        $days = max(1, (new \DateTime($startDate))->diff(new \DateTime($endDate))->days);

        // Verificar que todos los equipos sigan disponibles
        $equipmentItems = Equipment::whereIn('id', $validated['equipment_ids'])->get();

        foreach ($equipmentItems as $eq) {
            if (!$eq->isAvailable()) {
                return redirect()->back()->withInput()
                    ->with('error', "El equipo '{$eq->name}' ya no está disponible.");
            }
        }

        $rental = DB::transaction(function () use ($validated, $startDate, $endDate, $days, $equipmentItems) {
            $rental = Rental::create([
                'client_id'   => auth()->id(),
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

        return redirect()->route('client.my-rentals')
            ->with('success', '¡Solicitud de alquiler enviada! Espera la aprobación del administrador.');
    }

    /** Cancela una solicitud de alquiler (solo si está pendiente) */
    public function cancelRental(Rental $rental)
    {
        if ($rental->client_id !== auth()->id()) {
            abort(403);
        }

        if (!$rental->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'Solo se pueden cancelar alquileres pendientes.');
        }

        $rental->update(['status' => Rental::STATUS_CANCELLED]);

        return redirect()->back()
            ->with('success', 'Solicitud cancelada correctamente.');
    }
}
