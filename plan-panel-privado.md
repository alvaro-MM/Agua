# Plan — Panel privado (área privada del negocio)

**Cliente:** Miguel — Electro Bombas MAPF, instalación, reparación y mantenimiento de bombas de agua.
**Documento hermano de** `plan-web-publica.md`, que en su sección 11 dejaba el panel fuera de alcance y remitía a "un documento aparte". Este es ese documento.

---

## 1. Por qué

Con la web pública en marcha quedaban dos problemas abiertos:

1. **Los leads se perdían.** El formulario de contacto guardaba en `contact_messages`, pero no había forma de leer esa tabla. Si fallaba el email de aviso, el mensaje quedaba enterrado.
2. **Miguel no podía tocar nada.** Servicios, catálogo, proyectos, teléfono, horario y NIF vivían en `config/site.php`. Cambiar un número de teléfono exigía tocar código y desplegar.

**Objetivo:** que Miguel y su equipo entren con usuario y contraseña, atiendan los mensajes que llegan de la web y editen el contenido y los datos de la empresa sin depender de nadie.

---

## 2. Alcance

### Entregado

| Módulo | Qué hace |
|---|---|
| **Acceso** | Panel en `/admin`, en español y con la marca de la web. Sin registro público: las altas las hace un administrador. Recuperación de contraseña por correo. |
| **Usuarios y roles** | Dos roles: `admin` (Miguel, acceso total) y `tecnico`. Alta, edición y asignación de rol desde el panel. |
| **Bandeja de mensajes** | Los mensajes del formulario con estado, notas internas, registro de quién los atendió y accesos rápidos por correo, teléfono y WhatsApp. |
| **Contenido** | Servicios, catálogo y proyectos con publicado/borrador, destacado en portada y papelera. Subida de imágenes optimizadas. |
| **Ajustes del sitio** | Datos de empresa, contacto y redes que alimentan cabecera, pie, WhatsApp, contacto, aviso legal y los datos estructurados de Google. |
| **Escritorio** | Mensajes nuevos, recibidos esta semana, atendidos y contenido publicado. |

### Fuera de alcance (decidido, no olvidado)

- **Facturación.** Ni facturas ni VeriFactu ni AEAT. Es un proyecto en sí mismo y conlleva responsabilidad legal.
- **CRM.** Clientes, presupuestos y partes de trabajo. Ver la hoja de ruta más abajo.
- **Área de clientes.** Que cada cliente vea sus documentos.
- **Ordenación manual del contenido.** El orden público es el de creación. Añadirla después es una migración con un `sort_order` y una tabla reordenable.
- **Doble factor (2FA).** Filament 5 lo trae de serie; se activa cuando Miguel lo pida.
- **Galerías multi-imagen** por producto o proyecto.

---

## 3. Decisiones y por qué

| Decisión | Motivo |
|---|---|
| **Filament 5** en vez de construir el panel a mano | Ahorra semanas en tablas, filtros, formularios y subida de ficheros. Filament 4 no llega a Laravel 13. |
| **spatie/laravel-permission** en vez de una columna `role` | Permite añadir roles y permisos sin migraciones cuando el equipo crezca. |
| **Ajustes en una fila con columnas tipadas**, no un key/value | El conjunto de campos es fijo y conocido: se valida mejor y Filament lo pinta como un formulario normal. |
| **Imágenes con intervention/image sobre GD** | Convierte a WebP y genera miniatura sin depender de binarios (`cwebp`, `jpegoptim`) instalados en el servidor. |
| **`image_path` admite ruta o URL absoluta** | Las imágenes de ejemplo del seeder siguen viéndose hasta que Miguel suba las suyas. |
| **La caché guarda arrays, no modelos** | Laravel trae `cache.serializable_classes` en `false` para protegerse de cadenas de gadgets si se filtra la `APP_KEY`. Guardar objetos rompía con cualquier driver que serialice. |
| **`config/site.php` se conserva** | Pasa a ser la semilla de arranque y la copia de referencia del contenido original. |

---

## 4. Cómo está montado

```
app/
├── Actions/StoreOptimizedImage.php     # subida: WebP + miniatura
├── Concerns/
│   ├── FlushesPublicCache.php          # invalida la caché al guardar
│   ├── HasImagePath.php                # accessor image_url
│   └── HasPublicationState.php         # scopes published() y featured()
├── Enums/ContactMessageStatus.php
├── Filament/
│   ├── Pages/SiteSettingsPage.php      # ajustes (fila única, no CRUD)
│   ├── Resources/                      # mensajes, servicios, productos, proyectos, usuarios
│   ├── Support/                        # piezas compartidas de tabla, imagen y avatar
│   └── Widgets/ResumenBandejaWidget.php
├── Models/                             # Service, Product, Project, SiteSettings, ContactMessage
├── Policies/                           # una por recurso, apoyadas en Permissions
├── Providers/Filament/AdminPanelProvider.php
└── Support/
    ├── Permissions.php                 # catálogo único de roles y permisos
    └── PublicContent.php               # lectura cacheada del contenido público
```

**Cómo llega el contenido a la web:** el panel escribe en la base de datos → `FlushesPublicCache` vacía las claves de `PublicContent` → la siguiente visita reconstruye la caché. Los ajustes se comparten a las vistas con un *view composer*; el contenido lo inyecta `PageController`.

---

## 5. Roles

| | `admin` (Miguel) | `tecnico` |
|---|---|---|
| Bandeja de mensajes | ver, gestionar, eliminar | ver y gestionar |
| Servicios, catálogo, proyectos | todo | solo consulta |
| Publicar y destacar | sí | no |
| Usuarios | sí | no |
| Ajustes del sitio | sí | no |

Dos salvaguardas: nadie se elimina a sí mismo y nunca se puede eliminar ni cambiar de rol al último administrador.

---

## 6. Lo que le queda a Miguel

Nada de esto es código: es material que sigue pendiente desde la fase pública.

- [ ] Logo en SVG o PNG de alta resolución (`public/favicon.ico` está vacío, 0 bytes).
- [ ] Fotos reales de catálogo y proyectos. Ahora hay imágenes de ejemplo de `loremflickr.com`.
- [ ] Datos reales en **Ajustes del sitio**: razón social, NIF, teléfono, WhatsApp, correo, dirección, código postal, ciudad y zonas. Los actuales son de relleno (`B00000000`, `+34 600 000 000`, `Tu Ciudad`).
- [ ] URL del mapa de Google Maps, si quiere mostrarlo en Contacto.
- [ ] Perfiles de Facebook e Instagram, si los tiene.

---

## 7. Hoja de ruta

Por orden de valor para el negocio:

1. **Clientes.** Ficha con datos, dirección e histórico. Enlazar los mensajes de la bandeja con el cliente que los envió.
2. **Presupuestos.** Líneas, totales, PDF y envío por correo. Estados: borrador, enviado, aceptado, rechazado.
3. **Partes de trabajo.** Qué se hizo, cuándo, quién y con qué material. Es lo que más valor da en campo y lo que pide el móvil.
4. **Catálogo ligado a almacén**, si llega a hacer falta.
5. **Facturación.** Solo si Miguel lo pide expresamente y asumiendo que en España implica **VeriFactu**: registro de facturación, hash encadenado, QR y envío a la AEAT. Merece su propio plan y su propia conversación sobre responsabilidad.

Antes de 1 y 2 conviene decidir si el panel se usará desde el móvil en obra, porque condiciona el diseño de los partes de trabajo.
