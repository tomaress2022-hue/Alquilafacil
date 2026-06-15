@extends('layouts.app')
@section('title', 'Nueva Solicitud de Alquiler')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-4">
    <a href="{{ route('client.catalog') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="mb-0"><i class="bi bi-plus-circle text-primary me-2"></i> Nueva Solicitud de Alquiler</h2>
        <p class="text-muted mb-0">Selecciona equipos y fechas</p>
    </div>
</div>

<form method="POST" action="{{ route('client.rentals.store') }}">
    @csrf

    <!-- PASO 1: FECHAS -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i> Paso 1 — Período de Alquiler</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha de fin <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <!-- Cálculo de días (JavaScript) -->
            <div id="days-info" class="alert alert-info mt-3 d-none">
                <i class="bi bi-info-circle me-2"></i>
                Duración del alquiler: <strong id="days-count"></strong> día(s)
            </div>
        </div>
    </div>

    <!-- PASO 2: SELECCIÓN DE EQUIPOS -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-tools me-2"></i> Paso 2 — Selección de Equipos
                <span class="badge bg-white text-primary ms-2" id="selected-count">0 seleccionados</span>
            </h5>
        </div>
        <div class="card-body">
            @error('equipment_ids')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <div class="row g-3">
                @foreach($availableEquipment as $eq)
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 equipment-option {{ ($selectedEquipment && $selectedEquipment->id === $eq->id) ? 'border-primary bg-primary bg-opacity-5' : '' }}"
                         style="cursor: pointer;" onclick="toggleEquipment(this, {{ $eq->id }})">

                        <div class="d-flex align-items-start gap-3">
                            <!-- Checkbox oculto -->
                            <input type="checkbox" name="equipment_ids[]"
                                   value="{{ $eq->id }}"
                                   id="eq-{{ $eq->id }}"
                                   class="equipment-checkbox"
                                   {{ ($selectedEquipment && $selectedEquipment->id === $eq->id) ? 'checked' : '' }}
                                   style="pointer-events:none;">

                            <!-- Info del equipo -->
                            <div class="flex-grow-1">
                                <strong>{{ $eq->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $eq->category->name }} · {{ $eq->code }}</small>
                                <br>
                                <span class="text-primary fw-bold">
                                    ${{ number_format($eq->daily_price, 0, ',', '.') }}/día
                                </span>
                            </div>

                            <!-- Indicador visual -->
                            <div class="selected-icon" style="display:none;">
                                <i class="bi bi-check-circle-fill text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PASO 3: NOTAS Y RESUMEN -->
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="bi bi-chat-text me-2 text-primary"></i> Paso 3 — Notas opcionales</h5>
        </div>
        <div class="card-body">
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="Indicaciones especiales, lugar de entrega, etc.">{{ old('notes') }}</textarea>
        </div>
    </div>

    <!-- RESUMEN DE PRECIO -->
    <div class="card border-primary mb-4" id="price-summary" style="display:none!important;">
        <div class="card-body bg-primary bg-opacity-5">
            <h5 class="text-primary"><i class="bi bi-calculator me-2"></i> Resumen estimado</h5>
            <div id="price-detail"></div>
        </div>
    </div>

    <!-- BOTÓN ENVIAR -->
    <div class="d-flex gap-3">
        <a href="{{ route('client.catalog') }}" class="btn btn-outline-secondary">
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary btn-lg flex-fill">
            <i class="bi bi-send me-2"></i> Enviar Solicitud
        </button>
    </div>
</form>

</div>
</div>
@endsection

@push('scripts')
<script>
// Datos de equipos para el cálculo de precio
const equipmentPrices = {
    @foreach($availableEquipment as $eq)
    {{ $eq->id }}: { name: '{{ addslashes($eq->name) }}', price: {{ $eq->daily_price }} },
    @endforeach
};

let selectedEquipment = new Set();
let days = 0;

// Toggle selección de equipo
function toggleEquipment(card, id) {
    const checkbox = document.getElementById('eq-' + id);
    const icon = card.querySelector('.selected-icon');

    if (selectedEquipment.has(id)) {
        selectedEquipment.delete(id);
        checkbox.checked = false;
        card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-5');
        icon.style.display = 'none';
    } else {
        selectedEquipment.add(id);
        checkbox.checked = true;
        card.classList.add('border-primary', 'bg-primary', 'bg-opacity-5');
        icon.style.display = 'block';
    }

    document.getElementById('selected-count').textContent = selectedEquipment.size + ' seleccionado(s)';
    updateSummary();
}

// Calcular días al cambiar fechas
function calculateDays() {
    const start = document.querySelector('[name=start_date]').value;
    const end   = document.querySelector('[name=end_date]').value;
    const info  = document.getElementById('days-info');

    if (start && end) {
        const diff = (new Date(end) - new Date(start)) / (1000 * 60 * 60 * 24);
        days = Math.max(0, diff);
        if (days > 0) {
            info.classList.remove('d-none');
            document.getElementById('days-count').textContent = days;
            updateSummary();
        } else {
            info.classList.add('d-none');
        }
    }
}

// Actualizar resumen de precios
function updateSummary() {
    const summary = document.getElementById('price-summary');
    const detail  = document.getElementById('price-detail');

    if (days > 0 && selectedEquipment.size > 0) {
        summary.style.removeProperty('display');
        let html = '<ul class="list-unstyled mb-2">';
        let total = 0;

        selectedEquipment.forEach(id => {
            const eq = equipmentPrices[id];
            const subtotal = eq.price * days;
            total += subtotal;
            html += `<li class="d-flex justify-content-between mb-1">
                <span>${eq.name} × ${days} día(s)</span>
                <strong>$${subtotal.toLocaleString('es-CO')}</strong>
            </li>`;
        });

        html += `</ul>
        <hr>
        <div class="d-flex justify-content-between fs-5 fw-bold text-primary">
            <span>Total estimado</span>
            <span>$${total.toLocaleString('es-CO')}</span>
        </div>
        <small class="text-muted">* El total final puede variar según aprobación del administrador.</small>`;

        detail.innerHTML = html;
    } else {
        summary.style.display = 'none';
    }
}

// Eventos
document.querySelector('[name=start_date]').addEventListener('change', calculateDays);
document.querySelector('[name=end_date]').addEventListener('change', calculateDays);

// Inicializar si hay equipo pre-seleccionado
@if($selectedEquipment)
    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('[onclick="toggleEquipment(this, {{ $selectedEquipment->id }})"]');
        if (!selectedEquipment.has({{ $selectedEquipment->id }})) {
            toggleEquipment(card, {{ $selectedEquipment->id }});
        }
    });
@endif
</script>
@endpush