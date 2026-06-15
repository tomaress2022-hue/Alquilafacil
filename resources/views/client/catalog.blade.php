@extends('layouts.app')
@section('title', 'Catálogo de Equipos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-grid text-primary"></i> Catálogo de Equipos</h2>
        <p class="text-muted mb-0">Equipos disponibles para alquiler</p>
    </div>
    <a href="{{ route('client.rentals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nueva Solicitud
    </a>
</div>

<!-- FILTROS -->
<div class="card mb-4 p-3">
    <form method="GET" action="{{ route('client.catalog') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-semibold">Buscar equipo</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Nombre del equipo..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Categoría</label>
            <select name="category_id" class="form-select">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search me-1"></i> Buscar
            </button>
        </div>
    </form>
</div>

<!-- GRID DE EQUIPOS -->
<div class="row g-4">
    @forelse($equipment as $eq)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card equipment-card h-100">
            <!-- Imagen del equipo -->
            <img src="{{ $eq->imageUrl() }}" class="card-img-top"
                 style="height: 200px; object-fit: cover;" alt="{{ $eq->name }}">

            <div class="card-body d-flex flex-column">
                <!-- Categoría -->
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2" style="width: fit-content;">
                    {{ $eq->category->name }}
                </span>

                <!-- Nombre y código -->
                <h6 class="card-title fw-bold mb-1">{{ $eq->name }}</h6>
                <small class="text-muted mb-2">Código: {{ $eq->code }}</small>

                <!-- Descripción truncada -->
                @if($eq->description)
                    <p class="card-text text-muted small mb-3" style="flex: 1;">
                        {{ Str::limit($eq->description, 80) }}
                    </p>
                @endif

                <!-- Precio y estado -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="fs-5 fw-bold text-primary">
                            ${{ number_format($eq->daily_price, 0, ',', '.') }}
                        </span>
                        <span class="text-muted small">/día</span>
                    </div>
                    {!! $eq->statusBadge() !!}
                </div>

                <!-- Botones de acción -->
                <div class="d-flex gap-2">
                    <a href="{{ route('client.equipment.detail', $eq) }}"
                       class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="bi bi-eye"></i> Ver más
                    </a>
                    @if($eq->isAvailable())
                        <a href="{{ route('client.rentals.create', ['equipment_id' => $eq->id]) }}"
                           class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-bag-plus"></i> Alquilar
                        </a>
                    @else
                        <button class="btn btn-secondary btn-sm flex-fill" disabled>
                            No disponible
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No hay equipos disponibles</h5>
            <p class="text-muted">Intenta con otro filtro o vuelve más tarde.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- PAGINACIÓN -->
<div class="d-flex justify-content-center mt-4">
    {{ $equipment->withQueryString()->links() }}
</div>
@endsection