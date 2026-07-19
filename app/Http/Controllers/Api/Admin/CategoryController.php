<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** GET /api/admin/categories */
    public function index()
    {
        $categories = Category::withCount('equipment')->latest()->paginate(10);

        return CategoryResource::collection($categories);
    }

    /** POST /api/admin/categories */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique'   => 'Ya existe una categoría con ese nombre.',
        ]);

        $category = Category::create($validated);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /** GET /api/admin/categories/{category} */
    public function show(Category $category)
    {
        $category->loadCount('equipment');

        return new CategoryResource($category);
    }

    /** PUT/PATCH /api/admin/categories/{category} */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    /** DELETE /api/admin/categories/{category} */
    public function destroy(Category $category)
    {
        if ($category->equipment()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar: la categoría tiene equipos asociados.',
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Categoría eliminada exitosamente.']);
    }
}
