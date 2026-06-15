@extends('layouts.app')
@section('title', 'Mis Alquileres')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-receipt text-primary"></i> Mis Alquileres</h2>
    <a href="{{ route('client.rentals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nueva Solicitud
    </a>
</div>

<!-- FILTROS POR ESTADO -->
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('client.my-rentals') }}"
       class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">Todos</a>
    <a href="{{ route('client.my-rentals', ['status' => 'pending']) }}"
       class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pendientes</a>
    <a href="{{ route('client.my-rentals', ['status' => 'active']) }}"
       class="btn btn-sm {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}">Activos</a>
    <a href="{{ route('client.my-rentals', ['status' => 'returned']) }}"
       class="btn btn-sm {{ request('status') === 'returned' ? 'btn-secondary' : 'btn-outline-secondary' }}">Devueltos</a>
    <a href="{{ route('client.my-rentals', ['status' => 'cancelled']) }}"
       class="btn btn-sm {{ request('status') === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' }}">Cancelados</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
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
                    <td>#{{ $rental->id }}</td>
                    <td class="small">
                        @foreach($rental->items as $item)
                            {{ $item->equipment->name }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="small">
                        {{ $rental->start_date->format('d/m/Y') }} → {{ $rental->end_date->format('d/m/Y') }}<br>
                        <span class="text-muted">{{ $rental->calculateDays() }} día(s)</span>
                    </td>
                    <td><strong>${{ number_format($rental->total_price, 0, ',', '.') }}</strong></td>
                    <td>{!! $rental->statusBadge() !!}</td>
                    <td>
                        @if($rental->canBeCancelled())
                        <form method="POST" action="{{ route('client.rentals.cancel', $rental) }}"
                              onsubmit="return confirm('¿Cancelar esta solicitud?')">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                        </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1"></i><br>
                        No tienes solicitudes de alquiler.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $rentals->withQueryString()->links() }}
    </div>
</div>
@endsection
