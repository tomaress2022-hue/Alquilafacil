#!/bin/sh
set -e

echo "Limpiando caches de config..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Variables de DB actuales:"
echo "DB_CONNECTION=$DB_CONNECTION"
echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_DATABASE=$DB_DATABASE"
echo "PORT=$PORT"

echo "Ejecutando migraciones..."
php artisan migrate --force || echo "ADVERTENCIA: las migraciones fallaron"

LISTEN_PORT="${PORT:-8080}"

echo "Iniciando servidor PHP integrado en el puerto $LISTEN_PORT..."
exec php -S 0.0.0.0:${LISTEN_PORT} -t public public/index.php
