@extends('layouts.app')
@section('title', $equipment->name)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.equipment.index') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="mb-0"><i class="bi bi-tools text-primary me-2"></i> {{ $equipment->name }}</h2>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <img src="{{ $equipment->imageUrl() }}" class="img-fluid rounded-3" alt="{{ $equipment->name }}">
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $equipment->category->name }}</span>
                        <h4 class="mb-0">{{ $equipment->name }}</h4>
                        <small class="text-muted">Código: {{ $equipment->code }}</small>
                    </div>
                    {!! $equipment->statusBadge() !!}
                </div>

                <p class="text-muted">{{ $equipment->description ?: 'Sin descripción.' }}</p>

                <div class="fs-4 fw-bold text-primary">
                    ${{ number_format($equipment->daily_price, 0, ',', '.') }}/día
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HISTORIAL DE ALQUILERES -->
<div class="card mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Historial de alquileres</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Días</th>
                    <th>Subtotal</th>
                    <th>Estado del alquiler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipment->rentalItems as $item)
                <tr>
                    <td>#{{ $item->rental->id }}</td>
                    <td>{{ $item->rental->client->name }}</td>
                    <td>{{ $item->days }}</td>
                    <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td>{!! $item->rental->statusBadge() !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Este equipo aún no ha sido alquilado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
