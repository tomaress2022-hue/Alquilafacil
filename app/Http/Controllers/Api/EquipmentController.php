<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /** GET /api/equipment — Catálogo con filtros (category_id, status, search) */
    public function index(Request $request)
    {
        $query = Equipment::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $equipment = $query->latest()->paginate(12);

        return EquipmentResource::collection($equipment);
    }

    /** GET /api/equipment/{equipment} — Detalle de un equipo */
    public function show(Equipment $equipment)
    {
        $equipment->load('category');

        return new EquipmentResource($equipment);
    }
}
