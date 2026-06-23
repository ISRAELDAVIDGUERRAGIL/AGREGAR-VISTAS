# Archivos de Vista - Proyecto Laravel

Vistas Blade, controladores API y JS para CRUD de **Cargos**, **Empleados** y **Funciones de Cargo** con autenticacion (Breeze + Sanctum).

## Requisitos previos

El proyecto donde copies estos archivos debe tener instalado:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade

composer require laravel/sanctum
php artisan install:api
```

Ademas necesita Tailwind CSS y Alpine.js (incluidos por Breeze). Las migraciones, modelos, FormRequests y Resources de:
- `Cargo`
- `Empleado`
- `FuncionCargo`

deben existir en el proyecto base.

## Como funciona el fetch (API desde JS)

El archivo `resources/js/app.js` expone un objeto global `window.api` que maneja todas las peticiones a la API con CSRF automatico:

```js
window.api = {
  listar:     (ruta, params = '') => apiRequest('GET',    `/${ruta}${params}`),
  crear:      (ruta, datos)        => apiRequest('POST',   `/${ruta}`, datos),
  actualizar: (ruta, id, datos)    => apiRequest('PUT',    `/${ruta}/${id}`, datos),
  eliminar:   (ruta, id)           => apiRequest('DELETE', `/${ruta}/${id}`),
};
```

La funcion `apiRequest`:
1. Lee el token CSRF de la cookie `XSRF-TOKEN`
2. Lo envia como header `X-XSRF-TOKEN` en POST/PUT/DELETE
3. Prefija todas las URLs con `/api`
4. Retorna el JSON parseado de la respuesta
5. Si la respuesta no es `ok`, lanza el error

Ejemplo de uso en las vistas Alpine:
```js
const data = await window.api.listar('cargos', '?page=2&nombre=Gerente');
await window.api.crear('empleados', { nombres: 'Juan', ... });
await window.api.actualizar('empleados', 5, { salario: 3000 });
await window.api.eliminar('funciones-cargo', 10);
```

## Rutas Web (vistas Blade)

| Metodo | Ruta              | Vista            | Auth | Descripcion            |
|--------|-------------------|------------------|------|------------------------|
| GET    | `/`               | welcome          | No   | Landing page           |
| GET    | `/dashboard`      | dashboard        | Si   | Panel principal        |
| GET    | `/profile`        | profile.edit     | Si   | Editar perfil          |
| PATCH  | `/profile`        | -                | Si   | Actualizar perfil      |
| DELETE | `/profile`        | -                | Si   | Eliminar cuenta        |
| GET    | `/cargos`         | cargos.index     | Si   | CRUD de Cargos         |
| GET    | `/empleados`      | empleados.index  | Si   | CRUD de Empleados      |
| GET    | `/funciones-cargo`| funciones.index  | Si   | CRUD de Funciones      |

## Rutas API (controladores)

Todas prefijadas con `/api/` y protegidas por Sanctum (SPA).

### /api/cargos
| Metodo | Ruta              | Filtros                      |
|--------|-------------------|------------------------------|
| GET    | `/api/cargos`     | `?nombre=&ids=&page=`        |
| POST   | `/api/cargos`     | Body: `nombre_cargo`, `descripcion` |
| GET    | `/api/cargos/{id}`| -                            |
| PUT    | `/api/cargos/{id}`| -                            |
| DELETE | `/api/cargos/{id}`| -                            |

### /api/empleados
| Metodo | Ruta                 | Filtros                                                    |
|--------|----------------------|------------------------------------------------------------|
| GET    | `/api/empleados`     | `?nombre=&estado=&cargo=&cargos=&salario_min=&salario_max=&page=` |
| POST   | `/api/empleados`     | Body: `id_cargo`, `nombres`, `apellidos`, `fecha_nacimiento`, `fecha_ingreso`, `salario`, `estado` |
| GET    | `/api/empleados/{id}`| -                                                          |
| PUT    | `/api/empleados/{id}`| -                                                          |
| DELETE | `/api/empleados/{id}`| -                                                          |

### /api/funciones-cargo
| Metodo | Ruta                       | Filtros                    |
|--------|----------------------------|----------------------------|
| GET    | `/api/funciones-cargo`     | `?id_cargo=&estado=&page=` |
| POST   | `/api/funciones-cargo`     | Body: `id_cargo`, `descripcion_funcion`, `estado` |
| GET    | `/api/funciones-cargo/{id}`| -                          |
| PUT    | `/api/funciones-cargo/{id}`| -                          |
| DELETE | `/api/funciones-cargo/{id}`| -                          |

Todas las respuestas GET incluyen paginacion con estructura:
```json
{
  "data": [...],
  "meta": {
    "total": 50,
    "per_page": 10,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 10
  }
}
```

## Instalacion para probar

1. Crear un proyecto Laravel nuevo:
```bash
composer create-project laravel/laravel proyecto-vista
cd proyecto-vista
```

2. Instalar Breeze con Blade:
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

3. Instalar Sanctum para API SPA:
```bash
composer require laravel/sanctum
php artisan install:api
```

4. Configurar `.env` con base de datos y ejecutar migraciones:
```bash
php artisan migrate
```

5. Copiar los archivos de este repo a las carpetas correspondientes:
   - `app/Http/Controllers/Api/` → 3 controladores
   - `resources/views/` → layouts, cargos, empleados, funciones
   - `resources/js/app.js` → reemplazar (o mergear el objeto `window.api`)
   - `resources/css/app.css` → mergear si es necesario
   - `routes/web.php` → mergear las rutas
   - `bootstrap/app.php` → mergear configuracion de middleware

6. Asegurar que existen los modelos, migraciones, FormRequests y Resources para `Cargo`, `Empleado` y `FuncionCargo`.

7. Compilar assets y levantar servidor:
```bash
npm install && npm run build
php artisan serve
```
