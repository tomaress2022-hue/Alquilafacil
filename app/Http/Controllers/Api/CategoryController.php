<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /** GET /api/categories — Lista todas las categorías (con conteo de equipos) */
    public function index()
    {
        $categories = Category::withCount('equipment')->latest()->get();

        return CategoryResource::collection($categories);
    }

    /** GET /api/categories/{category} — Detalle de una categoría */
    public function show(Category $category)
    {
        $category->loadCount('equipment');

        return new CategoryResource($category);
    }
}
