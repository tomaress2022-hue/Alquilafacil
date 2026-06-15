# ============================================================
# Script para aplicar las correcciones de AlquilaFacil
# ============================================================
# INSTRUCCIONES:
# 1. Extrae el zip "alquilafacil-corregido.zip" en una carpeta,
#    por ejemplo: C:\Users\Daniela\AlquilaFacil-corregido
# 2. Coloca este script (aplicar-correcciones.ps1) dentro de
#    esa misma carpeta extraída (junto a app, routes, etc.)
# 3. Abre PowerShell, navega a esa carpeta y ejecuta:
#       .\aplicar-correcciones.ps1
# 4. Te pedira la ruta de tu proyecto real (donde esta tu .env)
# ============================================================

$origen = $PSScriptRoot

$destino = Read-Host "Ruta de tu proyecto AlquilaFacil (ej: C:\Users\Daniela\AlquilaFacil)"

if (-not (Test-Path $destino)) {
    Write-Host "ERROR: La ruta '$destino' no existe." -ForegroundColor Red
    exit
}

if (-not (Test-Path (Join-Path $destino ".env"))) {
    Write-Host "ADVERTENCIA: No se encontro un archivo .env en '$destino'." -ForegroundColor Yellow
    $confirmar = Read-Host "¿Seguro que esta es la carpeta correcta de tu proyecto? (s/n)"
    if ($confirmar -ne "s") { exit }
}

# Lista de archivos y carpetas modificados/creados
$rutas = @(
    "app\Http\Controllers\Admin\AdminController.php",
    "app\Http\Controllers\Auth\RegisteredUserController.php",
    "app\Http\Controllers\Client\ClientController.php",
    "app\Models\Equipment.php",
    "routes\web.php",
    "resources\views\admin\dashboard.blade.php",
    "resources\views\auth\register.blade.php",
    "resources\views\client\dashboard.blade.php",
    "resources\views\welcome.blade.php",
    "resources\views\admin\categories",
    "resources\views\admin\equipment",
    "resources\views\admin\rentals\show.blade.php",
    "resources\views\client\equipment-detail.blade.php",
    "resources\views\client\my-rentals.blade.php"
)

Write-Host ""
Write-Host "Copiando archivos corregidos a: $destino" -ForegroundColor Cyan
Write-Host ""

foreach ($ruta in $rutas) {
    $origenPath  = Join-Path $origen $ruta
    $destinoPath = Join-Path $destino $ruta

    if (-not (Test-Path $origenPath)) {
        Write-Host "  [OMITIDO] No existe en origen: $ruta" -ForegroundColor Yellow
        continue
    }

    $destinoDir = Split-Path $destinoPath -Parent
    if (-not (Test-Path $destinoDir)) {
        New-Item -ItemType Directory -Path $destinoDir -Force | Out-Null
    }

    if (Test-Path $origenPath -PathType Container) {
        Copy-Item -Path $origenPath -Destination $destinoDir -Recurse -Force
        Write-Host "  [OK] Carpeta copiada: $ruta" -ForegroundColor Green
    } else {
        Copy-Item -Path $origenPath -Destination $destinoPath -Force
        Write-Host "  [OK] Archivo copiado: $ruta" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Limpiando cache de Laravel..." -ForegroundColor Cyan
Push-Location $destino
php artisan view:clear
php artisan config:clear
php artisan route:clear
Pop-Location

Write-Host ""
Write-Host "Listo. Ahora corre 'php artisan serve' y refresca el navegador (Ctrl+F5)." -ForegroundColor Green
