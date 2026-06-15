@extends('layouts.app')
@section('title', 'Mi Dashboard')

@section('content')
<div class="mb-4">
    <h2 class="mb-1"><i class="bi bi-house text-primary"></i> ¡Bienvenido, {{ auth()->user()->name }}!</h2>
    <p class="text-muted mb-0">Este es tu panel de control de AlquilaFácil.</p>
</div>

<!-- TARJETAS DE ESTADÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card blue p-3">
            <small class="text-muted">Total solicitudes</small>
            <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card yellow p-3">
            <small class="text-muted">Pendientes</small>
            <div class="fs-3 fw-bold">{{ $stats['pending'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card green p-3">
            <small class="text-muted">Activos</small>
            <div class="fs-3 fw-bold">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card red p-3">
            <small class="text-muted">Devueltos</small>
            <div class="fs-3 fw-bold">{{ $stats['returned'] }}</div>
        </div>
    </div>
</div>

<!-- ACCIONES RÁPIDAS -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="{{ route('client.catalog') }}" class="card p-3 text-decoration-none text-dark h-100 d-block">
            <i class="bi bi-grid text-primary fs-2"></i>
            <h5 class="mt-2 mb-1">Ver catálogo</h5>
            <p class="text-muted small mb-0">Explora los equipos disponibles para alquilar.</p>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('client.rentals.create') }}" class="card p-3 text-decoration-none text-dark h-100 d-block">
            <i class="bi bi-plus-circle text-primary fs-2"></i>
            <h5 class="mt-2 mb-1">Nueva solicitud</h5>
            <p class="text-muted small mb-0">Crea una nueva solicitud de alquiler.</p>
        </a>
    </div>
</div>

<!-- ÚLTIMOS ALQUILERES -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Últimas solicitudes</h5>
        <a href="{{ route('client.my-rentals') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Equipos</th>
                    <th>Período</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                <tr>
                    <td>#{{ $rental->id }}</td>
                    <td class="small">
                        @foreach($rental->items as $item)
                            {{ $item->equipment->name }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="small">
                        {{ $rental->start_date->format('d/m/Y') }} → {{ $rental->end_date->format('d/m/Y') }}
                    </td>
                    <td><strong>${{ number_format($rental->total_price, 0, ',', '.') }}</strong></td>
                    <td>{!! $rental->statusBadge() !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Aún no tienes solicitudes de alquiler.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
