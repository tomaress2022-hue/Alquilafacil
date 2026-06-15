<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlquilaFácil — Alquiler de equipos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e40af 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: #fff;
        }
        .card-glass {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            backdrop-filter: blur(6px);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="bi bi-tools"></i> AlquilaFácil
                </h1>
                <p class="lead">
                    Plataforma para alquilar equipos de informática, fotografía, sonido, video y herramientas.
                </p>
            </div>
        </div>

        <div class="row justify-content-center g-4 mb-5">
            <div class="col-md-4">
                <div class="card-glass p-4 h-100 text-center">
                    <i class="bi bi-grid fs-1 mb-2"></i>
                    <h5>Catálogo completo</h5>
                    <p class="small mb-0">Explora equipos disponibles por categoría y disponibilidad.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-glass p-4 h-100 text-center">
                    <i class="bi bi-calendar-check fs-1 mb-2"></i>
                    <h5>Solicita tu alquiler</h5>
                    <p class="small mb-0">Selecciona fechas y equipos, y envía tu solicitud en minutos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-glass p-4 h-100 text-center">
                    <i class="bi bi-shield-check fs-1 mb-2"></i>
                    <h5>Gestión segura</h5>
                    <p class="small mb-0">Administración de aprobaciones, devoluciones y disponibilidad.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-5">
                        <i class="bi bi-speedometer2 me-2"></i> Ir a mi Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5 me-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5">
                        <i class="bi bi-person-plus me-2"></i> Registrarme
                    </a>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
