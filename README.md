# AlquilaFacil

**Estudiante:** Nicolas Hernandez

## Descripción

AlquilaFacil es un sistema web para la gestión de alquiler de equipos, desarrollado con **Laravel 12**. Permite a los administradores registrar categorías y equipos, y a los clientes solicitar alquileres a través de un catálogo. El flujo de un alquiler pasa por los estados *pendiente → activo → devuelto* (o *cancelado*), y el sistema actualiza automáticamente la disponibilidad de cada equipo según ese estado.

El proyecto maneja dos roles de usuario:

- **Admin**: gestiona categorías, equipos, y aprueba o registra la devolución de alquileres.
- **Cliente**: navega el catálogo, solicita alquileres y consulta el historial de los suyos.

## Tablas implementadas y sus relaciones

| Tabla | Descripción | Campos clave |
|---|---|---|
| `users` | Usuarios del sistema (admin o cliente) | `role`, `documento`, `phone` |
| `categories` | Categorías que agrupan equipos (ej. "Sonido", "Herramientas") | `name`, `description` |
| `equipment` | Equipos disponibles para alquilar | `category_id`, `code`, `daily_price`, `status`, `image` |
| `rentals` | Solicitudes/alquileres hechos por un cliente | `client_id`, `status`, `start_date`, `end_date`, `total_price` |
| `rental_items` | Ítems de cada alquiler (un alquiler puede incluir varios equipos) | `rental_id`, `equipment_id`, `daily_price`, `days`, `subtotal` |

### Relaciones

- **`categories` 1 → N `equipment`**: una categoría tiene muchos equipos.
- **`users` 1 → N `rentals`**: un usuario (cliente) puede tener muchos alquileres.
- **`rentals` 1 → N `rental_items`**: un alquiler puede incluir varios equipos.
- **`equipment` 1 → N `rental_items`**: un equipo puede aparecer en varios ítems de alquiler a lo largo del tiempo.
- **`rental_items`** actúa como tabla intermedia entre `rentals` y `equipment`, guardando el precio y los días de ese alquiler en particular (para conservar el precio histórico aunque el equipo cambie de precio después).

```
users (1) ──< (N) rentals (1) ──< (N) rental_items >── (N) (1) equipment >── (N) (1) categories
```

## Instrucciones para correr localmente

### Requisitos previos

- PHP 8.2 o superior
- Composer
- Node.js y npm

### Pasos

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio>
cd Alquilafacil

# 2. Instalar dependencias de PHP
composer install

# 3. Copiar el archivo de entorno y generar la clave de la app
cp .env.example .env
php artisan key:generate

# 4. Configurar la base de datos en .env
# Por defecto usa SQLite, no requiere instalar nada más:
touch database/database.sqlite

# 5. Ejecutar las migraciones (crea las tablas)
php artisan migrate

# (Opcional) Cargar datos de ejemplo
php artisan db:seed

# 6. Instalar dependencias de frontend y compilar assets
npm install
npm run build

# 7. Levantar el servidor local
php artisan serve
```

La aplicación quedará disponible en `http://localhost:8000`.

## Capturas de pantalla

> Agrega aquí las capturas de tu proyecto corriendo localmente (dashboard, catálogo, formulario de alquiler, panel de admin, etc.). Puedes tomarlas abriendo `http://localhost:8000` después del paso anterior.

| Pantalla | Captura |
|---|---|
| Login | `![Login](docs/screenshots/login.png)` |
| Catálogo de equipos | `![Catálogo](docs/screenshots/catalogo.png)` |
| Dashboard admin | `![Dashboard admin](docs/screenshots/dashboard-admin.png)` |
| Mis alquileres (cliente) | `![Mis alquileres](docs/screenshots/mis-alquileres.png)` |

Guarda las imágenes en una carpeta `docs/screenshots/` dentro del repositorio y reemplaza las rutas de arriba por los nombres reales de tus archivos.
