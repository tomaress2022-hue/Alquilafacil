FROM php:8.3-cli

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node (para compilar assets con Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN npm install && npm run build

RUN php artisan storage:link || true

COPY start.sh /start.sh
RUN chmod +x /start.sh
RUN echo "=== CONTENIDO DE start.sh ===" && cat /start.sh && echo "=== FIN start.sh ==="

EXPOSE 8080

CMD ["/start.sh"]
