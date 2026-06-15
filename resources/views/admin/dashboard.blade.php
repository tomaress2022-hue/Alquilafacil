@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="mb-4">
    <h2 class="mb-1"><i class="bi bi-speedometer2 text-primary"></i> Panel de Administración</h2>
    <p class="text-muted mb-0">Resumen general de AlquilaFácil.</p>
</div>

<!-- TARJETAS DE ESTADÍSTICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card blue p-3">
            <small class="text-muted">Equipos totales</small>
            <div class="fs-3 fw-bold">{{ $stats['equipment_total'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card green p-3">
            <small class="text-muted">Disponibles</small>
            <div class="fs-3 fw-bold">{{ $stats['equipment_available'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card yellow p-3">
            <small class="text-muted">En alquiler</small>
            <div class="fs-3 fw-bold">{{ $stats['equipment_rented'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card red p-3">
            <small class="text-muted">En mantenimiento</small>
            <div class="fs-3 fw-bold">{{ $stats['equipment_maintenance'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card blue p-3">
            <small class="text-muted">Categorías</small>
            <div class="fs-3 fw-bold">{{ $stats['categories_total'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card yellow p-3">
            <small class="text-muted">Solicitudes pendientes</small>
            <div class="fs-3 fw-bold">{{ $stats['rentals_pending'] }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card green p-3">
            <small class="text-muted">Alquileres activos</small>
            <div class="fs-3 fw-bold">{{ $stats['rentals_active'] }}</div>
        </div>
    </div>
</div>

<!-- ÚLTIMOS ALQUILERES -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Últimas solicitudes de alquiler</h5>
        <a href="{{ route('admin.rentals.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Equipos</th>
                    <th>Período</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentRentals as $rental)
                <tr>
                    <td>#{{ $rental->id }}</td>
                    <td>{{ $rental->client->name }}</td>
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
                    <td colspan="6" class="text-center py-4 text-muted">
                        No hay solicitudes registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
