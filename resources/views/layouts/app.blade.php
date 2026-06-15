<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AlquilaFácil') — AlquilaFácil</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --accent: #f59e0b;
        }
        body { background-color: #f8fafc; }
        .navbar-brand { font-weight: 700; font-size: 1.4rem; }
        .sidebar { min-height: calc(100vh - 56px); background: #1e293b; }
        .sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin: 2px 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff; background: var(--primary);
        }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 12px; }
        .stat-card { border-left: 4px solid; border-radius: 12px; }
        .stat-card.green  { border-color: #22c55e; }
        .stat-card.yellow { border-color: #f59e0b; }
        .stat-card.red    { border-color: #ef4444; }
        .stat-card.blue   { border-color: #3b82f6; }
        .badge { font-size: 0.8rem; }
        .equipment-card:hover { transform: translateY(-2px); transition: .2s; }
    </style>

    @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--primary);">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <i class="bi bi-tools"></i> AlquilaFácil
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ auth()->user()->name }}
                            <span class="badge ms-1 {{ auth()->user()->isAdmin() ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ auth()->user()->isAdmin() ? 'Admin' : 'Cliente' }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR (solo para usuarios autenticados) -->
        @auth
        <div class="col-auto d-none d-md-flex flex-column sidebar p-3" style="width: 220px;">
            <ul class="nav flex-column">

                @if(auth()->user()->isAdmin())
                    {{-- Menú administrador --}}
                    <li class="nav-item mb-2">
                        <small class="text-uppercase text-secondary px-2" style="font-size:.7rem;">
                            Administración
                        </small>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.equipment.index') }}"
                           class="nav-link {{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}">
                            <i class="bi bi-tools me-2"></i> Equipos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}"
                           class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tag me-2"></i> Categorías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rentals.index') }}"
                           class="nav-link {{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check me-2"></i> Alquileres
                        </a>
                    </li>
                @else
                    {{-- Menú cliente --}}
                    <li class="nav-item mb-2">
                        <small class="text-uppercase text-secondary px-2" style="font-size:.7rem;">
                            Mi cuenta
                        </small>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.dashboard') }}"
                           class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house me-2"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.catalog') }}"
                           class="nav-link {{ request()->routeIs('client.catalog') ? 'active' : '' }}">
                            <i class="bi bi-grid me-2"></i> Catálogo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.my-rentals') }}"
                           class="nav-link {{ request()->routeIs('client.my-rentals') ? 'active' : '' }}">
                            <i class="bi bi-receipt me-2"></i> Mis Alquileres
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.rentals.create') }}"
                           class="nav-link {{ request()->routeIs('client.rentals.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo Alquiler
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        @endauth

        <!-- ÁREA PRINCIPAL DE CONTENIDO -->
        <main class="col p-4">

            {{-- Mensajes flash de éxito/error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="alert alert-warning alert-dismissible fade show">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Contenido de cada vista --}}
            @yield('content')
        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>