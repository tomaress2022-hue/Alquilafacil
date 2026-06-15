<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Rental;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'equipment_total'     => Equipment::count(),
            'equipment_available' => Equipment::where('status', 'available')->count(),
            'equipment_rented'    => Equipment::where('status', 'rented')->count(),
            'equipment_maintenance' => Equipment::where('status', 'maintenance')->count(),
            'categories_total'    => Category::count(),
            'rentals_pending'     => Rental::where('status', 'pending')->count(),
            'rentals_active'      => Rental::where('status', 'active')->count(),
        ];

        $recentRentals = Rental::with(['client', 'items.equipment'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRentals'));
    }
}
