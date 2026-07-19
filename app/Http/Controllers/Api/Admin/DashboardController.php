<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;

class DashboardController extends Controller
{
    /** GET /api/admin/dashboard */
    public function index()
    {
        $stats = [
            'equipment_total'       => Equipment::count(),
            'equipment_available'   => Equipment::where('status', 'available')->count(),
            'equipment_rented'      => Equipment::where('status', 'rented')->count(),
            'equipment_maintenance' => Equipment::where('status', 'maintenance')->count(),
            'categories_total'      => Category::count(),
            'rentals_pending'       => Rental::where('status', 'pending')->count(),
            'rentals_active'        => Rental::where('status', 'active')->count(),
        ];

        $recentRentals = Rental::with(['client', 'items.equipment'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats'          => $stats,
            'recent_rentals' => RentalResource::collection($recentRentals),
        ]);
    }
}
