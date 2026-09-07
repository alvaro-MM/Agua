# Electro Bombas MAPF

Web corporativa y panel de gestión de **Electro Bombas MAPF**, empresa de instalación, reparación y mantenimiento de bombas de agua.

- **Web pública** — inicio, servicios, catálogo, proyectos, sobre nosotros, contacto y páginas legales, con SEO y datos estructurados. Plan: [`plan-web-publica.md`](plan-web-publica.md).
- **Panel de gestión** en `/admin` — bandeja de mensajes del formulario, contenido del sitio y ajustes de la empresa. Plan: [`plan-panel-privado.md`](plan-panel-privado.md).

## Stack

Laravel 13 · PHP 8.3+ · Blade · Tailwind CSS 4 · Vite · Filament 5 · spatie/laravel-permission

## Requisitos

- PHP 8.3 o superior con la extensión **GD** (las imágenes del panel se convierten a WebP).
- Composer y Node 20+.
- MySQL o MariaDB en producción. En local basta con SQLite.

## Puesta en marcha

```bash
composer setup            # instala dependencias, crea .env, genera la clave y migra
php artisan storage:link  # publica las imágenes que se suben desde el panel
php artisan db:seed       # roles, primer administrador y contenido inicial
composer dev              # servidor + cola + logs + Vite
```

En local, deja `DB_CONNECTION=sqlite` en el `.env`; para MySQL, descomenta el resto del bloque de base de datos.

## El primer administrador

Lo crea `AdminUserSeeder` a partir del `.env`:

```dotenv
ADMIN_NAME="Miguel"
ADMIN_EMAIL=miguel@ejemplo.com
ADMIN_PASSWORD=              # si lo dejas vacío, se genera una y se muestra al sembrar
```

Después se entra en `/admin`. **No hay registro público**: los usuarios siguientes los da de alta un administrador desde el propio panel.

Para crear otro administrador desde consola:

```bash
php artisan make:filament-user
php artisan tinker --execute="App\Models\User::where('email','...')->first()->assignRole('admin');"
```

## Roles

| | `admin` | `tecnico` |
|---|---|---|
| Bandeja de mensajes | ver, gestionar y eliminar | ver y gestionar |
| Contenido del sitio | todo | solo consulta |
| Usuarios y ajustes | sí | no |

Los permisos se definen en `app/Support/Permissions.php` y los siembra `RolesAndPermissionsSeeder`. Tras añadir uno nuevo, relanza ese seeder: es idempotente.

## Contenido del sitio

Servicios, catálogo, proyectos y los datos de la empresa viven en la base de datos y se editan en `/admin`. `config/site.php` es solo la **semilla de arranque** y la copia de referencia del contenido original; las vistas ya no lo leen.

`SiteContentSeeder` vuelca ese fichero a la base de datos y es idempotente: relanzarlo restaura los textos originales sin duplicar filas.

Lo que se guarda en el panel se ve en la web al momento: `PublicContent` cachea las consultas y el trait `FlushesPublicCache` vacía esa caché al guardar.

## Imágenes

Las que se suben desde el panel se convierten a WebP, se limitan a 1600 px de ancho y generan una miniatura, en `storage/app/public/{catalogo,proyectos}`. Requiere `php artisan storage:link`.

Las imágenes de ejemplo del seeder son URLs externas; `image_path` acepta tanto una ruta del disco público como una URL absoluta.

## Tests y estilo

```bash
php artisan test      # SQLite en memoria
vendor/bin/pint       # formato del código
```

## Despliegue

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

`composer install` ejecuta `filament:upgrade`, que republica los assets del panel: por eso no están en el repositorio.
