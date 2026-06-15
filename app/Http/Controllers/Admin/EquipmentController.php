<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    /** Lista todos los equipos con filtros */
    public function index(Request $request)
    {
        $query = Equipment::with('category');

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Búsqueda por nombre o código
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $equipment  = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('admin.equipment.index', compact('equipment', 'categories'));
    }

    /** Formulario para crear equipo */
    public function create()
    {
        $categories = Category::all();
        return view('admin.equipment.create', compact('categories'));
    }

    /** Guarda el nuevo equipo */
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

        // Manejo de imagen
        if ($request->hasFile('image')) {
            // Guarda en storage/app/public/equipment/
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        Equipment::create($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', '¡Equipo registrado exitosamente!');
    }

    /** Muestra detalle de un equipo */
    public function show(Equipment $equipment)
    {
        $equipment->load('category', 'rentalItems.rental.client');
        return view('admin.equipment.show', compact('equipment'));
    }

    /** Formulario para editar equipo */
    public function edit(Equipment $equipment)
    {
        $categories = Category::all();
        return view('admin.equipment.edit', compact('equipment', 'categories'));
    }

    /** Actualiza el equipo */
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

        // Si sube nueva imagen, eliminar la anterior
        if ($request->hasFile('image')) {
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            $validated['image'] = $request->file('image')->store('equipment', 'public');
        }

        $equipment->update($validated);

        return redirect()->route('admin.equipment.index')
            ->with('success', '¡Equipo actualizado exitosamente!');
    }

    /** Elimina el equipo */
    public function destroy(Equipment $equipment)
    {
        // Verificar que no esté en un alquiler activo
        if ($equipment->status === 'rented') {
            return redirect()->route('admin.equipment.index')
                ->with('error', 'No se puede eliminar un equipo que está en alquiler.');
        }

        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }

        $equipment->delete();

        return redirect()->route('admin.equipment.index')
            ->with('success', 'Equipo eliminado.');
    }
}