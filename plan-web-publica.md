# Plan — Web pública (zona comercial)

**Cliente:** Miguel — empresa de instalación, reparación y mantenimiento de bombas de agua.  
**Alcance de este documento:** solo la web pública. El panel privado (ERP/CRM) queda fuera y se planificará aparte.

**Estado actual del repo:** Laravel 13 + Tailwind 4 + Vite. Proyecto recién iniciado (solo `welcome`).

---

## 1. Objetivo

Poner en producción una web corporativa profesional que:

- Dé imagen de confianza y capte clientes.
- Explique servicios y productos.
- Permita contacto fácil (formulario + WhatsApp).
- Esté preparada para Google (SEO local básico).

**Entregable:** web en dominio real, usable por Miguel desde el día uno.

---

## 2. Alcance

### Incluido

| Página / función | Descripción |
|------------------|-------------|
| **Inicio** | Hero, servicios destacados, llamada a la acción, confianza (años, zonas, etc.). |
| **Servicios** | Instalación, reparación, mantenimiento. Una página índice + detalle por servicio (o secciones en una sola página). |
| **Catálogo** | Bombas y accesorios. Contenido estático al inicio (sin depender del almacén del ERP). |
| **Proyectos** | Portfolio de trabajos realizados (fotos + breve descripción). |
| **Sobre nosotros** | Historia, equipo, valores, zona de actuación. |
| **Contacto** | Formulario + datos (teléfono, email, dirección, horario). |
| **WhatsApp** | Botón flotante `wa.me` con mensaje predefinido. |
| **Legal** | Aviso legal + política de privacidad (RGPD, formulario). |
| **SEO** | Meta tags, Open Graph, `sitemap.xml`, `robots.txt`, Schema.org `LocalBusiness`. |

### Fuera de alcance (por ahora)

- Panel de administración / Filament.
- Catálogo dinámico ligado a almacén.
- Pasarela de pago, área de clientes, multi-idioma.
- App móvil.
- Blog (salvo que Miguel lo pida explícitamente).

---

## 3. Stack (solo web pública)

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 13 (PHP 8.3+) |
| Vistas | Blade |
| Estilos | Tailwind CSS 4 (ya en el proyecto) |
| Assets | Vite |
| Formulario contacto | Laravel Mail + validación + rate limiting |
| Anti-spam | Honeypot y/o reCAPTCHA (decidir con Miguel) |
| BBDD | MySQL/MariaDB o SQLite en local; solo para mensajes de contacto al inicio |
| Imágenes | WebP donde sea posible, lazy loading |

No instalar Filament ni dependencias del ERP en esta fase.

---

## 4. Estructura de código

```
app/
├── Http/
│   └── Controllers/
│       ├── HomeController.php
│       ├── ServiceController.php
│       ├── CatalogController.php
│       ├── ProjectController.php
│       ├── AboutController.php
│       └── ContactController.php
├── Models/
│   └── ContactMessage.php          # mensajes del formulario (opcional pero recomendado)
└── Mail/
    └── ContactFormSubmitted.php

resources/
├── views/
│   ├── layouts/
│   │   └── public.blade.php        # header, footer, nav, WhatsApp, meta SEO
│   ├── components/                 # botones, cards, secciones reutilizables
│   └── public/
│       ├── home.blade.php
│       ├── services/
│       ├── catalog/
│       ├── projects/
│       ├── about.blade.php
│       ├── contact.blade.php
│       └── legal/
│           ├── privacy.blade.php
│           └── legal-notice.blade.php
├── css/app.css
└── js/app.js

routes/web.php                        # solo rutas públicas
public/
├── robots.txt
└── (sitemap generado o estático)
```

**Contenido estático vs dinámico:** al principio servicios, catálogo y proyectos pueden ser arrays/config o seeders. Más adelante, si hace falta, Miguel podrá editarlos desde un panel — no es requisito de esta fase.

---

## 5. Fases (solo web pública)

### Fase 0 — Preparación (1 reunión + materiales)

**Con Miguel:**

- [ ] Confirmar alcance de este plan y lista de “fuera de alcance”.
- [ ] Recopilar: logo, colores, textos, teléfono, WhatsApp, email, dirección, horario, zona geográfica.
- [ ] Fotos: proyectos, equipo, vehículos, trabajos en campo.
- [ ] Lista inicial de servicios y productos del catálogo (aunque sea en Excel).
- [ ] Dominio y hosting (o decisión: VPS + Forge, PaaS, etc.).

**Técnico:**

- [ ] Configurar `.env` (app, mail, BBDD).
- [ ] Rama `main` desplegable; convención de ramas `feature/*`.

---

### Fase 1 — Diseño y layout base

- [ ] Layout público: cabecera con navegación, pie con datos de contacto y enlaces legales.
- [ ] Diseño responsive (móvil primero).
- [ ] Componentes: botones CTA, tarjetas de servicio, galería de proyectos.
- [ ] Botón flotante WhatsApp.
- [ ] Tipografía y paleta acorde a la marca.

**Criterio de hecho:** navegar entre páginas vacías o con placeholder sin romper el diseño.

---

### Fase 2 — Páginas de contenido

- [ ] **Inicio** — hero, servicios resumidos, proyectos destacados, CTA contacto.
- [ ] **Servicios** — instalación, reparación, mantenimiento.
- [ ] **Catálogo** — listado de bombas/accesorios (estático).
- [ ] **Proyectos** — galería con fotos y descripciones.
- [ ] **Sobre nosotros** — texto corporativo.
- [ ] **Contacto** — datos + mapa (embed opcional) + formulario.

**Criterio de hecho:** todo el contenido acordado visible y revisado por Miguel.

---

### Fase 3 — Formulario de contacto

- [ ] Validación servidor (nombre, email, teléfono, mensaje, consentimiento RGPD).
- [ ] Guardar mensaje en BBDD (`contact_messages`).
- [ ] Email de notificación a Miguel.
- [ ] Página de confirmación / mensaje de éxito.
- [ ] Rate limiting + honeypot (o reCAPTCHA).
- [ ] Política de privacidad enlazada desde el checkbox.

**Criterio de hecho:** envío real probado en staging; Miguel recibe el email.

---

### Fase 4 — SEO y rendimiento

- [ ] `title` y `meta description` únicos por página.
- [ ] Open Graph (imagen, título, descripción para redes).
- [ ] URLs limpias (`/servicios`, `/contacto`, etc.).
- [ ] `robots.txt` y `sitemap.xml`.
- [ ] JSON-LD `LocalBusiness` (nombre, teléfono, dirección, horario, área servida).
- [ ] Imágenes optimizadas (WebP, dimensiones, `alt`).
- [ ] Lighthouse: objetivo ≥ 85 en móvil (Performance, SEO, Accessibility).

**Post-lanzamiento (Miguel o con ayuda):**

- [ ] Google Search Console — verificar dominio.
- [ ] Google Business Profile — ficha de empresa actualizada con URL de la web.

---

### Fase 5 — Legal y despliegue

- [ ] Aviso legal y política de privacidad (plantilla adaptada al negocio).
- [ ] HTTPS (Let's Encrypt).
- [ ] Entorno staging para validación de Miguel.
- [ ] Despliegue a producción en dominio definitivo.
- [ ] Email transaccional configurado (Postmark, SES, Mailgun o SMTP del hosting).

**Criterio de hecho:** web accesible en producción; formulario y WhatsApp funcionando; Miguel da el visto bueno.

---

## 6. Rutas previstas

```
GET  /                      → Inicio
GET  /servicios             → Listado servicios
GET  /servicios/{slug}      → Detalle servicio (opcional)
GET  /catalogo              → Catálogo productos
GET  /proyectos             → Portfolio
GET  /sobre-nosotros        → Sobre la empresa
GET  /contacto              → Formulario contacto
POST /contacto              → Envío formulario
GET  /privacidad            → Política de privacidad
GET  /aviso-legal           → Aviso legal
GET  /sitemap.xml           → Sitemap
```

---

## 7. Contenido que hay que pedir a Miguel

| Material | Uso |
|----------|-----|
| Logo (SVG/PNG alta resolución) | Cabecera, favicon, OG image |
| Textos de cada servicio | Páginas de servicios |
| Ficha de productos (nombre, foto, descripción breve) | Catálogo |
| 6–12 fotos de proyectos reales | Portfolio e inicio |
| Texto “Sobre nosotros” | Página corporativa |
| Teléfono, WhatsApp, email, dirección, horario | Contacto, footer, Schema.org |
| Ciudad/zonas de actuación | SEO local y textos |
| NIF/CIF y razón social | Aviso legal |

---

## 8. SEO local (resumen práctico)

Palabras clave orientativas (ajustar ciudad con Miguel):

- "reparación bombas de agua [ciudad]"
- "mantenimiento bombas [ciudad]"
- "instalación bombas de agua [ciudad]"

En cada página: un H1 claro, párrafos con intención local, imágenes con `alt` descriptivo.

---

## 9. Seguridad y RGPD (solo lo público)

- HTTPS obligatorio.
- Consentimiento explícito en el formulario (checkbox + enlace a privacidad).
- No exponer emails en texto plano si se quiere evitar spam (opcional: solo formulario).
- Rate limiting en `POST /contacto`.
- `.env` fuera del repositorio.

---

## 10. Riesgos y mitigación

| Riesgo | Mitigación |
|--------|------------|
| Falta de contenido/fotos de Miguel | Checklist en Fase 0; placeholders solo en staging. |
| Scope creep (“¿y un blog?”, “¿y tienda?”) | Fuera de alcance documentado; cambios = nueva fase. |
| Spam en formulario | Honeypot + rate limit; reCAPTCHA si persiste. |
| SEO lento en aparecer | Search Console + Business Profile; expectativas realistas (semanas/meses). |

---

## 11. Qué viene después (no incluido aquí)

Cuando la web pública esté en producción y validada:

1. Panel privado (CRM, presupuestos, facturación…).
2. Catálogo conectado al almacén (opcional).
3. Área para que Miguel edite proyectos/catálogo sin tocar código.

Eso se planifica en un documento aparte.

---

## 12. Checklist de arranque

- [ ] Reunión con Miguel: cerrar alcance y recopilar materiales (Fase 0).
- [ ] Crear layout y componentes base (Fase 1).
- [ ] Implementar las 6 páginas con contenido (Fase 2).
- [ ] Formulario de contacto + email (Fase 3).
- [ ] SEO técnico + optimización imágenes (Fase 4).
- [ ] Legal + despliegue producción (Fase 5).
- [ ] Entrega y formación breve a Miguel (cómo compartir la web, WhatsApp, etc.).

---

> **Resumen:** una sola entrega incremental — web corporativa completa en producción. Sin ERP, sin VeriFactu, sin Filament. Contenido estático al inicio; dinámico solo para mensajes de contacto. Prioridad: que Miguel tenga presencia online profesional lo antes posible.
