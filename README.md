# Católica School — HTML / CSS / Tailwind / JS puro

Versión estática y completa del sitio, lista para servir.

## Estructura

```
catolica-html/
├── index.html                      # Home con hero, valores, formulario, campus, partners, testimonios
├── pages/                          # 10 páginas internas
│   ├── quienes-somos.html
│   ├── propuesta-pedagogica.html
│   ├── reglamentos.html
│   ├── infraestructura.html
│   ├── proceso-admision.html
│   ├── traslados.html
│   ├── plataforma-postulantes.html
│   ├── preguntas-frecuentes.html
│   ├── trabaja-con-nosotros.html
│   └── noticias.html
├── css/styles.css                  # Tokens HSL, fuentes, animaciones
├── js/
│   ├── tailwind.config.js          # Config del CDN (colores, fuentes)
│   ├── partials.js                 # Inyecta TopBanner, Navbar, Footer, WhatsApp, SocialSidebar
│   └── main.js                     # Interacciones: dropdowns, FAQ, formulario, scroll reveal
├── fonts/                          # Bree Bold/Regular, ClanPro News, HelveticaNeue
├── assets/                         # Imágenes hero, infraestructura, noticias, logo
├── partners/                       # Logos de aliados
└── testimonials/                   # Fotos de testimonios
```

## Cómo abrirlo

**IMPORTANTE:** las fuentes locales requieren un servidor HTTP. No abras los `.html` con doble click.

```bash
# Opción 1: Python
python3 -m http.server 8000

# Opción 2: Node
npx serve .

# Luego visita http://localhost:8000
```

## Características incluidas

- ✅ **Tailwind CSS** vía CDN con configuración custom (colores HSL, tipografías)
- ✅ **Tipografía corporativa**: Bree (títulos y cuerpo), HelveticaNeue (CTAs)
- ✅ **Navbar responsive** con dropdowns desktop (hover + delay 150ms) y submenús móviles (acordeón)
- ✅ **Banner superior** cerrable
- ✅ **FAQ acordeón** con animación de altura suave (vanilla JS, single-open)
- ✅ **Formulario** con validación HTML nativa + handler JS
- ✅ **Scroll reveal** con `IntersectionObserver` (clase `.reveal`)
- ✅ **Animaciones**: `fade-in-up`, `fade-in`, `float` (botón WhatsApp), `slide-down` (dropdowns)
- ✅ **Botón WhatsApp** flotante con animación de flotación
- ✅ **Sidebar social** lateral (desktop)
- ✅ **Footer** con mapa embebido, contacto y enlaces
- ✅ **Hover effects**: zoom de imágenes, cambios de color, sombras

## Personalización

- **Colores**: edita las variables HSL en `css/styles.css` (`:root`)
- **Fuentes**: reemplaza los archivos en `fonts/` y ajusta `@font-face`
- **Contenido del nav y footer**: edita `js/partials.js`
- **Scripts del formulario**: edita el handler en `js/main.js` (`#registration-form`)

## Notas

- El video de fondo del formulario carga desde Pexels (CDN externo).
- El mapa de Google Maps está embebido vía iframe.
- El botón "Descargar" en `/reglamentos.html` es un placeholder (no descarga PDF real).
