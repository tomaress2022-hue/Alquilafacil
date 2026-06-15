#!/bin/sh
set -e

echo "Limpiando caches de config (para leer variables de entorno frescas)..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Variables de DB actuales:"
echo "DB_CONNECTION=$DB_CONNECTION"
echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_DATABASE=$DB_DATABASE"

echo "Ejecutando migraciones..."
php artisan migrate --force || echo "ADVERTENCIA: las migraciones fallaron"

echo "Iniciando servidor en el puerto ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
