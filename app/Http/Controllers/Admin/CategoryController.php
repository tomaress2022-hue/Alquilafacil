<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** Lista todas las categorías */
    public function index()
    {
        // withCount carga el número de equipos por categoría sin N+1
        $categories = Category::withCount('equipment')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /** Formulario para crear categoría */
    public function create()
    {
        return view('admin.categories.create');
    }

    /** Guarda la nueva categoría */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique'   => 'Ya existe una categoría con ese nombre.',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', '¡Categoría creada exitosamente!');
    }

    /** Formulario para editar categoría */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /** Actualiza la categoría */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', '¡Categoría actualizada exitosamente!');
    }

    /** Elimina la categoría */
    public function destroy(Category $category)
    {
        // Verificar que no tenga equipos asociados
        if ($category->equipment()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'No se puede eliminar: la categoría tiene equipos asociados.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}