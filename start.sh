#!/bin/sh
echo "######## START.SH VERSION 4 EJECUTANDOSE ########"
set -e
 
echo "DB_CONNECTION=$DB_CONNECTION"
echo "DB_HOST=$DB_HOST"
echo "PORT=$PORT"
 
echo "Limpiando config cache (no requiere DB)..."
php artisan config:clear
 
echo "Ejecutando migraciones..."
php artisan migrate --force || echo "ADVERTENCIA: migraciones fallaron"
 
LISTEN_PORT="${PORT:-8080}"
echo "######## LANZANDO PHP -S EN PUERTO $LISTEN_PORT ########"
exec php -S 0.0.0.0:${LISTEN_PORT} -t public public/index.php
 