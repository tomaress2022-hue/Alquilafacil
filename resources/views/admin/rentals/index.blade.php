@extends('layouts.app')
@section('title', 'Gestión de Alquileres')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-clipboard-check text-primary"></i> Gestión de Alquileres</h2>
</div>

<!-- Filtros por estado -->
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('admin.rentals.index') }}"
       class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
        Todos
    </a>
    <a href="{{ route('admin.rentals.index', ['status' => 'pending']) }}"
       class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
        <i class="bi bi-hourglass"></i> Pendientes
    </a>
    <a href="{{ route('admin.rentals.index', ['status' => 'active']) }}"
       class="btn btn-sm {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}">
        <i class="bi bi-play-circle"></i> Activos
    </a>
    <a href="{{ route('admin.rentals.index', ['status' => 'returned']) }}"
       class="btn btn-sm {{ request('status') === 'returned' ? 'btn-secondary' : 'btn-outline-secondary' }}">
        <i class="bi bi-check2-all"></i> Devueltos
    </a>
</div>

<div class="card">
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                <tr>
                    <td><span class="text-muted">#{{ $rental->id }}</span></td>
                    <td>
                        <strong>{{ $rental->client->name }}</strong><br>
                        <small class="text-muted">{{ $rental->client->phone }}</small>
                    </td>
                    <td>
                        @foreach($rental->items as $item)
                            <div class="small">
                                <i class="bi bi-dot text-primary"></i>
                                {{ $item->equipment->name }}
                            </div>
                        @endforeach
                    </td>
                    <td class="small">
                        {{ $rental->start_date->format('d/m/Y') }} →
                        {{ $rental->end_date->format('d/m/Y') }}<br>
                        <span class="text-muted">{{ $rental->calculateDays() }} día(s)</span>
                    </td>
                    <td><strong>${{ number_format($rental->total_price, 0, ',', '.') }}</strong></td>
                    <td>{!! $rental->statusBadge() !!}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.rentals.show', $rental) }}"
                               class="btn btn-sm btn-outline-secondary" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>

                            @if($rental->status === 'pending')
                            <form method="POST"
                                  action="{{ route('admin.rentals.approve', $rental) }}"
                                  onsubmit="return confirm('¿Aprobar este alquiler?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success" title="Aprobar">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endif

                            @if($rental->status === 'active')
                            <form method="POST"
                                  action="{{ route('admin.rentals.return', $rental) }}"
                                  onsubmit="return confirm('¿Registrar devolución de todos los equipos?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-info text-white" title="Registrar devolución">
                                    <i class="bi bi-box-arrow-in-left"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1"></i><br>
                        No hay alquileres registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $rentals->links() }}
    </div>
</div>
@endsection