# AlquilaFacil API

Se agregó una capa de API REST (JSON) sobre el proyecto Laravel existente, usando
**Laravel Sanctum** (tokens Bearer) para autenticación. Las vistas Blade y rutas
web originales se dejaron intactas; la API vive en paralelo bajo `/api`.

## 1. Instalación

```bash
composer install
composer require laravel/sanctum   # si no se instaló ya vía composer.json
cp .env.example .env
php artisan key:generate
php artisan migrate
```

La migración `create_personal_access_tokens_table` ya está incluida en
`database/migrations/`, no hace falta publicarla de nuevo.

## 2. Autenticación

Todas las rutas (excepto `/api/register` y `/api/login`) requieren el header:

```
Authorization: Bearer {token}
Accept: application/json
```

### Registro
```
POST /api/register
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "documento": "12345678",
  "phone": "3001234567"
}
```
Respuesta: `{ "user": {...}, "token": "1|xxxx..." }`

### Login
```
POST /api/login
{ "email": "juan@example.com", "password": "secret123" }
```

### Logout
```
POST /api/logout   (auth)
```

### Usuario actual
```
GET /api/me   (auth)
```

## 3. Perfil

| Método | Ruta | Descripción |
|---|---|---|
| GET | /api/profile | Ver perfil |
| PUT/PATCH | /api/profile | Actualizar perfil |
| DELETE | /api/profile | Eliminar cuenta (requiere `password`) |

## 4. Catálogo (cualquier usuario autenticado)

| Método | Ruta | Descripción |
|---|---|---|
| GET | /api/categories | Lista de categorías |
| GET | /api/categories/{id} | Detalle |
| GET | /api/equipment?category_id=&status=&search= | Catálogo paginado |
| GET | /api/equipment/{id} | Detalle de equipo |

## 5. Admin (rol `admin`)

| Método | Ruta | Descripción |
|---|---|---|
| GET | /api/admin/dashboard | Estadísticas + últimos alquileres |
| GET/POST/PUT/DELETE | /api/admin/categories[/{id}] | CRUD categorías |
| GET/POST/PUT/DELETE | /api/admin/equipment[/{id}] | CRUD equipos (imagen vía `multipart/form-data`) |
| GET | /api/admin/rentals?status= | Lista de alquileres |
| GET | /api/admin/rentals/{id} | Detalle |
| PATCH | /api/admin/rentals/{id}/approve | Aprobar solicitud |
| PATCH | /api/admin/rentals/{id}/return | Registrar devolución |

## 6. Cliente (rol `client`)

| Método | Ruta | Descripción |
|---|---|---|
| GET | /api/client/dashboard | Resumen de alquileres |
| GET | /api/rentals?status= | "Mis alquileres" |
| POST | /api/rentals | Crear solicitud de alquiler |
| GET | /api/rentals/{id} | Detalle (solo propio) |
| PATCH | /api/rentals/{id}/cancel | Cancelar (solo si está pendiente) |

### Crear alquiler
```json
POST /api/rentals
{
  "start_date": "2026-07-10",
  "end_date": "2026-07-15",
  "equipment_ids": [1, 3],
  "notes": "Entrega en obra"
}
```

## 7. Notas técnicas

- Para subir imágenes de equipo, usar `multipart/form-data` y, si tu cliente HTTP
  no soporta PUT/PATCH con archivos, envía `POST` con el campo `_method=PUT`.
- El middleware `role:admin` / `role:client` (en `app/Http/Middleware/RoleMiddleware.php`)
  ahora usa `$request->user()` para funcionar tanto con sesión web como con Sanctum.
- Todas las respuestas de listados usan paginación estándar de Laravel
  (`data`, `links`, `meta`).
- Los errores de validación devuelven `422` con el formato estándar de Laravel
  (`{"message": "...", "errors": {...}}`).
