@extends('layouts.app')
@section('title', 'Alquiler #' . $rental->id)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="mb-0"><i class="bi bi-clipboard-check text-primary me-2"></i> Alquiler #{{ $rental->id }}</h2>
    <span class="ms-3">{!! $rental->statusBadge() !!}</span>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-uppercase text-muted small mb-3">Cliente</h6>
                <p class="mb-1"><strong>{{ $rental->client->name }}</strong></p>
                <p class="mb-1 small text-muted">{{ $rental->client->email }}</p>
                <p class="mb-0 small text-muted">{{ $rental->client->phone ?? 'Sin teléfono' }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="text-uppercase text-muted small mb-3">Período</h6>
                <p class="mb-1"><i class="bi bi-calendar-event me-1"></i> {{ $rental->start_date->format('d/m/Y') }} → {{ $rental->end_date->format('d/m/Y') }}</p>
                <p class="mb-0 text-muted small">{{ $rental->calculateDays() }} día(s)</p>
            </div>
        </div>

        @if($rental->notes)
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="text-uppercase text-muted small mb-2">Notas</h6>
                <p class="mb-0">{{ $rental->notes }}</p>
            </div>
        </div>
        @endif

        <!-- ACCIONES -->
        <div class="d-flex gap-2 mt-3">
            @if($rental->status === 'pending')
            <form method="POST" action="{{ route('admin.rentals.approve', $rental) }}"
                  onsubmit="return confirm('¿Aprobar este alquiler?')" class="flex-fill">
                @csrf @method('PATCH')
                <button class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i> Aprobar</button>
            </form>
            @endif

            @if($rental->status === 'active')
            <form method="POST" action="{{ route('admin.rentals.return', $rental) }}"
                  onsubmit="return confirm('¿Registrar devolución?')" class="flex-fill">
                @csrf @method('PATCH')
                <button class="btn btn-info text-white w-100"><i class="bi bi-box-arrow-in-left me-1"></i> Registrar devolución</button>
            </form>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Equipos solicitados</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Equipo</th>
                            <th>Categoría</th>
                            <th>Precio/día</th>
                            <th>Días</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rental->items as $item)
                        <tr>
                            <td>{{ $item->equipment->name }}</td>
                            <td>{{ $item->equipment->category->name }}</td>
                            <td>${{ number_format($item->daily_price, 0, ',', '.') }}</td>
                            <td>{{ $item->days }}</td>
                            <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td class="fw-bold text-primary">${{ number_format($rental->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
