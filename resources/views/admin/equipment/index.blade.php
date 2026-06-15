@extends('layouts.app')
@section('title', 'Equipos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-tools text-primary"></i> Equipos</h2>
    <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Equipo
    </a>
</div>

<!-- FILTROS -->
<div class="card mb-4 p-3">
    <form method="GET" action="{{ route('admin.equipment.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Buscar</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Nombre o código..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Categoría</label>
            <select name="category_id" class="form-select">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Estado</label>
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Disponible</option>
                <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>En alquiler</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
    </form>
</div>

<!-- GRID DE EQUIPOS -->
<div class="row g-4">
    @forelse($equipment as $eq)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card equipment-card h-100">
            <img src="{{ $eq->imageUrl() }}" class="card-img-top"
                 style="height: 180px; object-fit: cover;" alt="{{ $eq->name }}">
            <div class="card-body d-flex flex-column">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2" style="width: fit-content;">
                    {{ $eq->category->name }}
                </span>
                <h6 class="card-title fw-bold mb-1">{{ $eq->name }}</h6>
                <small class="text-muted mb-2">Código: {{ $eq->code }}</small>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-primary">
                        ${{ number_format($eq->daily_price, 0, ',', '.') }}/día
                    </span>
                    {!! $eq->statusBadge() !!}
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('admin.equipment.show', $eq) }}" class="btn btn-sm btn-outline-secondary flex-fill" title="Ver">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.equipment.edit', $eq) }}" class="btn btn-sm btn-outline-primary flex-fill" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.equipment.destroy', $eq) }}"
                          onsubmit="return confirm('¿Eliminar este equipo?')" class="flex-fill">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No hay equipos registrados</h5>
        </div>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $equipment->withQueryString()->links() }}
</div>
@endsection
