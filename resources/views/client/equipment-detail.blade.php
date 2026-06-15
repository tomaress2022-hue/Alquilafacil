@extends('layouts.app')
@section('title', $equipment->name)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('client.catalog') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="mb-0">{{ $equipment->name }}</h2>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <img src="{{ $equipment->imageUrl() }}" class="img-fluid rounded-3" alt="{{ $equipment->name }}">
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $equipment->category->name }}</span>
                <h4>{{ $equipment->name }}</h4>
                <small class="text-muted">Código: {{ $equipment->code }}</small>

                <p class="text-muted mt-3">{{ $equipment->description ?: 'Sin descripción disponible.' }}</p>

                <div class="d-flex justify-content-between align-items-center my-3">
                    <span class="fs-3 fw-bold text-primary">
                        ${{ number_format($equipment->daily_price, 0, ',', '.') }}
                        <span class="fs-6 text-muted">/día</span>
                    </span>
                    {!! $equipment->statusBadge() !!}
                </div>

                @if($equipment->isAvailable())
                    <a href="{{ route('client.rentals.create', ['equipment_id' => $equipment->id]) }}"
                       class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-bag-plus me-2"></i> Solicitar alquiler
                    </a>
                @else
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        No disponible actualmente
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
