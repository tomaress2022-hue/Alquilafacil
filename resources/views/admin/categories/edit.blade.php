@extends('layouts.app')
@section('title', 'Editar Categoría')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">

<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="mb-0"><i class="bi bi-tag text-primary me-2"></i> Editar Categoría</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="description" rows="3"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-check-circle me-1"></i> Actualizar Categoría
                </button>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection
