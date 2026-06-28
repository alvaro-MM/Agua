# Guía de Gestión del Proyecto — Sistema de Gestión para Empresa de Bombas de Agua

**Cliente:** Miguel — nueva empresa de instalación, reparación y mantenimiento de bombas de agua.
**Stack elegido:** Laravel (backend + panel privado) + web pública.

Esta guía es el documento maestro para **gestionar** el proyecto de principio a fin: cómo organizarlo, qué decisiones técnicas tomar, en qué orden construir, cómo facturar legalmente en España (VeriFactu) y cómo entregarlo y mantenerlo. No es un cronograma por fechas, sino una guía de alcance, arquitectura, fases por entregables y riesgos.

---

## 1. Visión general

El proyecto tiene **dos grandes bloques**:

1. **Zona pública (web corporativa + SEO):** la cara comercial. Capta clientes y da imagen profesional.
2. **Panel privado (ERP/CRM en Laravel):** el verdadero valor. Gestiona clientes, presupuestos, facturación legal, gastos, trabajos de campo, almacén y estadísticas.

El objetivo de negocio de Miguel es **digitalizar toda la operativa** de una empresa de servicios técnicos: del aviso del cliente → orden de trabajo → material consumido → presupuesto → factura (VeriFactu) → cobro → estadística de rentabilidad.

### Principio rector de la gestión
> Entregar valor de forma incremental. Cada fase debe dejar al cliente algo **usable en producción**, no esperar al "gran lanzamiento final".

---

## 2. Alcance del proyecto

### 2.1 Zona pública
- Página de inicio.
- Servicios (instalación, reparación, mantenimiento).
- Catálogo de bombas y accesorios.
- Proyectos realizados (portfolio).
- Sobre la empresa.
- Contacto con formulario.
- Botón flotante de WhatsApp.
- SEO básico (indexación en Google).

### 2.2 Panel privado
- **CRM:** clientes, empresas, contactos, historial de trabajos.
- **Presupuestos:** crear, enviar PDF, convertir a factura.
- **Facturación:** facturas, rectificativas, series, cobros, estados, **VeriFactu**.
- **Gastos:** categorías, subcategorías, proveedores, facturas de compra, estadísticas, comparativa mensual/anual.
- **Trabajos:** avisos, órdenes de trabajo, técnico asignado, estado, fotografías, material utilizado, firma del cliente.
- **Almacén:** productos, bombas, repuestos, stock, entradas/salidas, alertas de stock mínimo.
- **Estadísticas:** beneficio mensual, gastos, facturación, clientes nuevos, trabajos realizados, productos más vendidos, márgenes.
- **Administración:** usuarios, roles y permisos, configuración, copias de seguridad.

### 2.3 Fuera de alcance (definir explícitamente con Miguel)
Conviene escribirlo para evitar el *scope creep*. Ejemplos de cosas que **no** entran salvo acuerdo: app móvil nativa, contabilidad completa (modelo 303/390), pasarela de pago online, multi-idioma, integración con bancos/PSD2.

---

## 3. Stack tecnológico recomendado

| Capa | Tecnología | Motivo |
|------|-----------|--------|
| Framework | **Laravel 11 (PHP 8.3+)** | Lo elegido; ecosistema maduro. |
| Panel admin | **Laravel Filament 3** | Genera CRUD, tablas, formularios, roles. Acelera enormemente el ERP. Alternativa: Livewire + Blade a mano, o Nova (de pago). |
| Auth/Roles | **spatie/laravel-permission** | Roles y permisos granulares. |
| Web pública | **Blade + Tailwind CSS** | Rápida, SEO-friendly. Alternativa: Inertia+Vue si se quiere SPA. |
| PDF | **barryvdh/laravel-dompdf** o **spatie/laravel-pdf** (Browsershot) | Presupuestos y facturas. |
| Cola/Jobs | **Laravel Queue** (database o Redis) | Envío de emails/PDF, firma VeriFactu asíncrona. |
| Búsqueda/Filtros | Eloquent + scopes; **Laravel Scout** opcional | Catálogo y CRM. |
| Almacenamiento ficheros | `storage` local + S3 compatible (fotos de trabajos, firmas) | Escalable. |
| Tests | **Pest/PHPUnit** | Calidad de facturación crítica. |
| Frontend assets | **Vite** | Estándar Laravel. |
| BBDD | **MySQL 8 / MariaDB** (o PostgreSQL) | Relacional, transacciones. |

**Recomendación clave:** usar **Filament** para todo el panel privado. Reduce drásticamente el esfuerzo en CRUD, tablas, filtros, permisos y dashboards, dejando tiempo para la lógica de negocio (facturación, VeriFactu, almacén).

---

## 4. Arquitectura y organización del código

Estructura modular por dominio dentro de Laravel:

```
app/
├── Models/                 # Eloquent: Client, Company, Quote, Invoice, WorkOrder, Product, Stock...
├── Filament/
│   ├── Resources/          # CRUD del panel (Clientes, Facturas, Productos...)
│   ├── Pages/              # Dashboards, estadísticas
│   └── Widgets/            # KPIs, gráficos
├── Services/
│   ├── Billing/            # Lógica de facturación, numeración por series
│   ├── VeriFactu/          # Firma, encadenado de registros, envío AEAT
│   ├── Pdf/                # Generación de PDF de presupuestos/facturas
│   └── Inventory/          # Movimientos de stock, alertas
├── Http/
│   └── Controllers/        # Web pública (landing, contacto, catálogo)
├── Policies/               # Autorización por modelo
└── Support/
database/
├── migrations/
├── seeders/                # Roles, categorías de gasto, series por defecto
└── factories/
resources/
├── views/public/           # Blade web pública
└── views/pdf/              # Plantillas PDF
```

**Separación pública / privada:** dos grupos de rutas y middleware. La web pública es accesible sin login; `/admin` (Filament) requiere autenticación + permisos.

---

## 5. Modelo de datos (entidades principales)

Relaciones clave a modelar desde el inicio (define las migraciones con cuidado, son el esqueleto del ERP):

- **Cliente** ↔ **Empresa** ↔ **Contacto** (un cliente puede ser particular o pertenecer a una empresa; una empresa tiene varios contactos).
- **Cliente** → **Avisos** → **Orden de trabajo** → (técnico, estado, fotos, líneas de material, firma).
- **Presupuesto** (cabecera + líneas) → se convierte en **Factura** (cabecera + líneas) → **Cobros**.
- **Factura** pertenece a una **Serie**; las **rectificativas** referencian la factura original.
- **Producto** (tipo: bomba / repuesto / accesorio) → **Stock** → **Movimientos** (entrada/salida) → ligados a órdenes de trabajo y a facturas de compra.
- **Gasto:** **Proveedor** → **Factura de compra** → líneas con **Categoría/Subcategoría**.
- **Registro VeriFactu** asociado 1:1 a cada factura (hash, hash anterior, estado de envío, respuesta AEAT).

> Consejo: usa **soft deletes** en documentos legales (facturas) — nunca borrar, solo anular/rectificar. Usa **transacciones** al emitir facturas y mover stock.

---

## 6. Fases del proyecto (entregables, no fechas)

Cada fase es un entregable usable. Ordenadas por valor y dependencias técnicas.

### Fase 0 — Preparación
- Reunión de requisitos con Miguel; cerrar alcance y lista de "fuera de alcance".
- Recopilar: datos fiscales de la empresa, logo, textos, fotos de proyectos, series de facturación deseadas, lista de servicios y catálogo inicial.
- Configurar repositorio, entornos (local/staging/producción), CI básico.
- Decidir hosting (ver §13).

### Fase 1 — Web pública + SEO (valor comercial rápido)
- Landing, servicios, catálogo (estático o ligado al almacén), proyectos, sobre la empresa.
- Formulario de contacto (con guardado + email + anti-spam).
- Botón WhatsApp flotante (`wa.me`).
- SEO básico: meta tags, Open Graph, `sitemap.xml`, `robots.txt`, datos estructurados (LocalBusiness), rendimiento (Lighthouse), Google Search Console + Google Business Profile.
- **Entregable:** web en producción captando clientes.

### Fase 2 — Núcleo del panel: Auth + CRM
- Login, usuarios, roles y permisos (admin, técnico, oficina).
- CRM: clientes, empresas, contactos, historial.
- **Entregable:** Miguel ya tiene su base de clientes digitalizada.

### Fase 3 — Almacén / catálogo de productos
- Productos (bombas, repuestos, accesorios), stock, entradas/salidas, alertas de stock mínimo.
- Necesario antes de facturar para poder añadir líneas de producto y descontar stock.
- **Entregable:** inventario controlado.

### Fase 4 — Presupuestos
- Crear presupuesto con líneas (productos + mano de obra), cálculo de IVA y márgenes.
- Generar PDF y enviarlo por email/WhatsApp.
- Estados (borrador, enviado, aceptado, rechazado).
- **Entregable:** presupuestos profesionales.

### Fase 5 — Facturación + VeriFactu (la fase crítica)
- Series, numeración correlativa, facturas, conversión presupuesto→factura.
- Facturas rectificativas.
- Cobros y estados (pendiente, parcial, cobrada, vencida).
- **Integración VeriFactu** (ver §8). Esta es la parte de mayor riesgo regulatorio: dejar margen y testear a fondo.
- **Entregable:** facturación legal conforme a normativa.

### Fase 6 — Trabajos (órdenes de trabajo de campo)
- Avisos → órdenes de trabajo, técnico asignado, estados.
- Fotografías, material utilizado (descuenta stock), firma del cliente (captura en pantalla).
- Vínculo orden → presupuesto/factura.
- **Entregable:** operativa de campo digitalizada.

### Fase 7 — Gastos
- Categorías, subcategorías, proveedores, facturas de compra.
- Estadísticas de gasto, comparativa mensual y anual.
- (Reutilizable del módulo de la autoescuela: adaptar entidades y vistas.)
- **Entregable:** control de costes.

### Fase 8 — Estadísticas / cuadro de mando
- Dashboard: beneficio mensual, gastos, facturación, clientes nuevos, trabajos realizados, productos más vendidos, márgenes.
- Widgets/gráficos en Filament.
- **Entregable:** visión global del negocio.

### Fase 9 — Administración y cierre
- Configuración general, gestión de usuarios/roles avanzada.
- Copias de seguridad automatizadas (ver §14).
- Documentación de usuario + formación a Miguel.
- **Entregable:** sistema completo y mantenible.

---

## 7. Metodología de gestión

- **Iterativa por fases** (tipo Scrum ligero / Kanban). Cada fase = mini-entrega con demo y validación de Miguel.
- **Tablero Kanban** (GitHub Projects, Trello o Linear): columnas *Backlog → En curso → Revisión → Hecho*.
- **Control de versiones:** ramas `feature/*` por módulo, PRs revisados, `main` siempre desplegable. Convención de commits clara.
- **Reuniones de validación** al cierre de cada fase con Miguel para aceptar el entregable y ajustar prioridades.
- **Registro de decisiones (ADR):** anotar decisiones técnicas y de alcance para evitar discusiones futuras.
- **Gestión de cambios:** todo cambio fuera del alcance acordado se documenta y se valora aparte (presupuesto adicional).
- **Definición de "Hecho" (DoD):** código revisado + tests de lo crítico + desplegado en staging + validado por cliente.

---

## 8. Integración VeriFactu (atención especial)

VeriFactu es el sistema de la AEAT para garantizar la integridad e inalterabilidad de las facturas (Ley Antifraude / Reglamento RD 1007/2023). Puntos clave de gestión:

- **Requisitos técnicos:**
  - Cada factura genera un **registro de facturación** firmado con **encadenamiento de hash** (cada registro incluye el hash del anterior → cadena inalterable).
  - Generación de **código QR** y mención "VERI*FACTU" en la factura.
  - Envío de los registros a la AEAT (modo VeriFactu) o conservación (modo no-VeriFactu con SIF certificado).
  - Uso de **certificado digital** de la empresa para la comunicación.
- **Recomendación de implementación:**
  - Aislar toda la lógica en `Services/VeriFactu` con interfaz clara; los registros se generan al **emitir** la factura (no al crearla en borrador).
  - Persistir el registro VeriFactu en su propia tabla (hash, hash anterior, payload, estado de envío, respuesta AEAT, timestamps). Las facturas emitidas son **inmutables**.
  - Procesar el envío a AEAT con **colas/reintentos** (la red de la AEAT puede fallar).
  - Considerar **librerías/SDK existentes** para VeriFactu en PHP/Laravel en lugar de implementar el protocolo desde cero — reduce muchísimo el riesgo. Evaluar también soluciones SaaS que exponen API de facturación verificable.
- **Gestión del riesgo:** es la parte legalmente sensible. Plan: (1) entorno de **pruebas/preproducción de la AEAT** antes de producción, (2) batería de tests sobre hash, encadenado y rectificativas, (3) revisión por un asesor fiscal/gestoría de Miguel de que las facturas cumplen formato legal.
- **Fechas de obligatoriedad:** verificar con fuentes oficiales/gestoría la fecha de entrada en vigor aplicable a Miguel antes de planificar (la normativa ha sufrido aplazamientos). No asumir; confirmar.

---

## 9. SEO de la zona pública

- Técnico: `title`/`meta description` por página, URLs limpias, encabezados semánticos, `sitemap.xml`, `robots.txt`, canonical.
- Datos estructurados **Schema.org `LocalBusiness`/`Plumber`** (servicio local) → mejora aparición en Google con dirección, teléfono, horario.
- **Google Business Profile** (ficha de empresa) + **Google Search Console**.
- Rendimiento (Core Web Vitals): imágenes optimizadas/WebP, lazy loading, caché.
- Contenido orientado a búsquedas locales: "reparación de bombas de agua en [ciudad]", "mantenimiento de bombas...".
- Accesibilidad básica (mejora SEO y usabilidad).

---

## 10. Seguridad, roles y permisos

- **Roles propuestos:** `admin` (Miguel/gestión total), `oficina` (CRM, presupuestos, facturas, gastos), `tecnico` (sus órdenes de trabajo, fotos, firma, material), `solo-lectura` opcional.
- `spatie/laravel-permission` + **Policies** de Laravel por modelo.
- HTTPS obligatorio, `.env` fuera del repo, credenciales/secretos en variables de entorno.
- Protección de datos (**RGPD**): los clientes son datos personales → política de privacidad, consentimiento en el formulario de contacto, derecho de supresión, registro de actividades.
- Rate limiting en formularios públicos y login; validación estricta de entradas.
- Auditoría/log de acciones sensibles (emisión/anulación de facturas).

---

## 11. Calidad y testing

- **Pest/PHPUnit** centrado en lo crítico: cálculo de totales/IVA, numeración de series, conversión presupuesto→factura, rectificativas, **encadenado VeriFactu**, movimientos de stock (no quedar en negativo).
- Tests de feature para flujos completos (crear orden → consumir material → facturar).
- CI (GitHub Actions): ejecutar tests + análisis estático (**Larastan/PHPStan**) + **Laravel Pint** en cada PR.
- Datos de prueba realistas con factories/seeders.

---

## 12. Entornos y despliegue

- **Tres entornos:** local (desarrollo), staging (validación con Miguel), producción.
- **Hosting:** VPS (Hetzner/DigitalOcean) con Laravel Forge/Ploi, o PaaS (Laravel Cloud, Render). Para una pyme, un VPS gestionado con Forge es coste-efectivo.
- Despliegue automatizado (deploy script / GitHub Actions), migraciones controladas, `php artisan optimize`.
- Dominio + certificado SSL (Let's Encrypt). Email transaccional (Postmark/SES/Mailgun) para PDFs y avisos.

---

## 13. Copias de seguridad

- **spatie/laravel-backup**: BBDD + ficheros (fotos, firmas, PDFs) programadas y enviadas a almacenamiento externo (S3/Backblaze).
- Estrategia 3-2-1 (3 copias, 2 medios, 1 fuera de sitio). Probar **restauraciones** periódicamente.
- Panel de administración con estado de la última copia + descarga manual.

---

## 14. Mantenimiento y soporte (post-entrega)

- Acuerdo de mantenimiento con Miguel: actualizaciones de seguridad de Laravel/dependencias, copias, monitorización de errores (**Sentry/Flare**), soporte ante incidencias.
- Plan de actualización de la normativa VeriFactu (la regulación evoluciona).
- Documentación de usuario + formación inicial. Vídeos cortos de los flujos clave.

---

## 15. Reutilización del módulo de la autoescuela (Gastos)

El módulo de **Gastos** (categorías, subcategorías, proveedores, facturas de compra, estadísticas) se puede portar:
- Extraer la lógica a un patrón reutilizable (idealmente un paquete interno o un conjunto de Resources Filament parametrizables).
- Renombrar entidades específicas de la autoescuela a términos genéricos.
- Reaprovechar las consultas de estadísticas (comparativa mensual/anual) cambiando solo el origen de datos.
- Revisar el modelo de datos para que encaje con el resto del ERP (proveedores compartidos con almacén).

---

## 16. Riesgos principales y mitigación

| Riesgo | Impacto | Mitigación |
|--------|---------|-----------|
| Complejidad/normativa **VeriFactu** | Alto (legal) | Usar SDK existente, entorno de pruebas AEAT, validar con gestoría, tests exhaustivos. |
| **Scope creep** (Miguel pide extras) | Medio | Alcance escrito, gestión de cambios, fuera-de-alcance explícito. |
| Errores en **cálculos/facturación** | Alto | Tests automáticos del dominio, transacciones, revisión contable. |
| Pérdida de datos | Alto | Backups automáticos + restauraciones probadas. |
| Datos personales (**RGPD**) | Medio | Política de privacidad, consentimientos, control de accesos. |
| Dependencia de un solo desarrollador | Medio | Documentación, código limpio, ADRs, repositorio ordenado. |

---

## 17. Checklist de próximos pasos

- [ ] Cerrar alcance y "fuera de alcance" con Miguel (firmar).
- [ ] Recopilar datos fiscales, logo, textos, fotos, catálogo, series.
- [ ] Confirmar con gestoría la fecha y modo de obligatoriedad **VeriFactu** para Miguel.
- [ ] Inicializar proyecto Laravel + Filament + Tailwind + spatie/permission.
- [ ] Definir y crear migraciones del modelo de datos núcleo.
- [ ] Configurar repositorio, ramas, CI (tests + Pint + Larastan).
- [ ] Levantar entornos (local/staging/producción) y hosting.
- [ ] Comenzar **Fase 1 (Web pública + SEO)** como primera entrega visible.

---

> **Resumen de gestión:** prioriza entregar la **web pública** primero (valor comercial inmediato), luego construye el ERP por fases con CRM → Almacén → Presupuestos → **Facturación/VeriFactu** → Trabajos → Gastos → Estadísticas → Administración. Trata VeriFactu como el componente de mayor riesgo y resérvale tests y validación fiscal. Usa Laravel + Filament para maximizar velocidad y mantén todo documentado y respaldado.
