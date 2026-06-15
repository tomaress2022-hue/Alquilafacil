@extends('layouts.app')
@section('title', 'Nuevo Equipo')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.equipment.index') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="mb-0"><i class="bi bi-tools text-primary me-2"></i> Nuevo Equipo</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.equipment.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code') }}" placeholder="EJ: CAM-001" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Selecciona una categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Precio por día <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="daily_price"
                           class="form-control @error('daily_price') is-invalid @enderror"
                           value="{{ old('daily_price') }}" required>
                    @error('daily_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>En alquiler</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Imagen</label>
                    <input type="file" name="image" accept="image/*"
                           class="form-control @error('image') is-invalid @enderror">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin.equipment.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-check-circle me-1"></i> Guardar Equipo
                </button>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection
