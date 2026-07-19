<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** GET /api/client/dashboard */
    public function index(Request $request)
    {
        $user = $request->user();

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

        return response()->json([
            'stats'   => $stats,
            'rentals' => RentalResource::collection($rentals),
        ]);
    }
}
