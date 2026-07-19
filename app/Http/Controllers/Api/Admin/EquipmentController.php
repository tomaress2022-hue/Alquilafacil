<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    /** GET /api/admin/equipment — Lista con filtros */
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

    /** POST /api/admin/equipment — Crea un equipo (multipart/form-data si incluye imagen) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:equipment,code',
            'description' => 'nullable|string',
            'daily_price' => 'required|numeric|min:0',
            'status'      => 'required|in:available,rented,maintenance',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'category_id.required' => 'Selecciona una categoría.',
            'code.unique'          => 'Ese código ya está en uso.',
            'daily_price.min'      => 'El precio no puede ser negativo.',
            'image.image'          => 'El archivo debe ser una imagen.',
            'image.max'            => 'La imagen no puede superar 2MB.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment = Equipment::create($validated);

        return (new EquipmentResource($equipment->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /** GET /api/admin/equipment/{equipment} */
    public function show(Equipment $equipment)
    {
        $equipment->load('category', 'rentalItems.rental.client');

        return new EquipmentResource($equipment);
    }

    /** PUT/PATCH /api/admin/equipment/{equipment} */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:equipment,code,' . $equipment->id,
            'description' => 'nullable|string',
            'daily_price' => 'required|numeric|min:0',
            'status'      => 'required|in:available,rented,maintenance',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment->update($validated);

        return new EquipmentResource($equipment->load('category'));
    }

    /** DELETE /api/admin/equipment/{equipment} */
    public function destroy(Equipment $equipment)
    {
        if ($equipment->status === 'rented') {
            return response()->json([
                'message' => 'No se puede eliminar un equipo que está en alquiler.',
            ], 422);
        }

        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }

        $equipment->delete();

        return response()->json(['message' => 'Equipo eliminado.']);
    }
}
