/* ===========================================================
   Partials reutilizables: TopBanner, Navbar, Footer, WhatsApp,
   SocialSidebar. Se inyectan en todas las páginas.
   =========================================================== */

const BASE = (() => {
  // Ajusta rutas relativas según la profundidad (raíz vs /pages)
  const inPages = window.location.pathname.includes('/pages/');
  return inPages ? '../' : './';
})();

/* ----- TopBanner ----- */
const TopBannerHTML = `
<div id="top-banner" class="bg-banner text-banner-foreground py-3 px-4 text-center text-sm font-medium relative">
  <span>
    ¡Asiste a nuestro <strong>Open Day</strong> este <strong>25 de Octubre</strong>!
    <a href="${BASE}index.html#admision" class="underline font-bold hover:opacity-80 transition-opacity">Regístrate aquí</a>
  </span>
  <button id="top-banner-close" aria-label="Cerrar banner"
          class="absolute right-4 top-1/2 -translate-y-1/2 hover:opacity-70 transition-opacity">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
  </button>
</div>`;

/* ----- Navbar ----- */
const nosotrosItems = [
  { label: '¿Quiénes somos?',         href: 'quienes-somos.html' },
  { label: 'Propuesta pedagógica',    href: 'propuesta-pedagogica.html' },
  { label: 'Reglamentos',             href: 'reglamentos.html' },
  { label: 'Nuestra Infraestructura', href: 'infraestructura.html' },
];
const admisionItems = [
  { label: 'Proceso de admisión',  href: 'proceso-admision.html' },
  { label: 'Traslados',            href: 'traslados.html' },
  { label: 'Preguntas frecuentes', href: 'preguntas-frecuentes.html' },
];

const dropdownItems = (items) => items.map(it => `
  <a href="${BASE}pages/${it.href}"
     class="block px-5 py-3 text-sm text-foreground hover:bg-muted hover:text-primary transition-colors">
    ${it.label}
  </a>`).join('');

const caretSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <polyline points="6 9 12 15 18 9"/></svg>`;

const NavbarHTML = `
<nav class="bg-background shadow-sm sticky top-0 z-50">
  <div class="container mx-auto flex items-center justify-between py-3 px-4">
    <a href="${BASE}index.html" class="flex items-center">
      <img src="${BASE}assets/logo-catolica.png" alt="Católica School" class="h-16 object-contain">
    </a>

    <!-- Desktop -->
    <ul class="hidden lg:flex items-center gap-8">
      <li><a href="${BASE}index.html" class="font-heading text-sm font-semibold text-foreground hover:text-primary transition-colors">Inicio</a></li>

      <li class="relative" data-dropdown="nosotros">
        <button class="font-heading text-sm font-semibold text-foreground hover:text-primary transition-colors flex items-center gap-1">
          Nosotros <span class="nav-caret">${caretSVG}</span>
        </button>
        <div class="nav-dropdown absolute top-full left-0 mt-2 w-56 bg-card rounded-xl shadow-lg border border-border py-2 z-50">
          ${dropdownItems(nosotrosItems)}
        </div>
      </li>

      <li class="relative" data-dropdown="admision">
        <button class="font-heading text-sm font-semibold text-foreground hover:text-primary transition-colors flex items-center gap-1">
          Admisión <span class="nav-caret">${caretSVG}</span>
        </button>
        <div class="nav-dropdown absolute top-full left-0 mt-2 w-56 bg-card rounded-xl shadow-lg border border-border py-2 z-50">
          ${dropdownItems(admisionItems)}
        </div>
      </li>

      <li><a href="${BASE}pages/trabaja-con-nosotros.html" class="font-heading text-sm font-semibold text-foreground hover:text-primary transition-colors">Trabaja con nosotros</a></li>
      <li><a href="${BASE}pages/noticias.html" class="font-heading text-sm font-semibold text-foreground hover:text-primary transition-colors">Noticias</a></li>
    </ul>

    <div class="hidden lg:flex items-center gap-3">
      <a href="${BASE}index.html#admision"
         class="bg-secondary text-secondary-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
        Admisión 2026
      </a>
      <a href="https://intranet.catolicaschool.edu.pe" target="_blank" rel="noopener"
         class="bg-accent text-accent-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
        Intranet
      </a>
    </div>

    <button id="burger" class="lg:hidden text-foreground" aria-label="Menu">
      <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
      <svg id="icon-close" class="hidden" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  <!-- Mobile -->
  <div id="mobile-menu" class="hidden lg:hidden bg-background border-t px-6 pb-6 pt-2 space-y-1">
    <a href="${BASE}index.html" class="block font-heading text-sm font-semibold text-foreground py-3">Inicio</a>

    <button class="mobile-toggle w-full flex items-center justify-between font-heading text-sm font-semibold text-foreground py-3" data-target="sub-nosotros">
      Nosotros <span class="mobile-caret">${caretSVG}</span>
    </button>
    <div id="sub-nosotros" class="mobile-sub pl-4 space-y-1">
      ${nosotrosItems.map(it => `<a href="${BASE}pages/${it.href}" class="block text-sm text-muted-foreground py-2 hover:text-primary">${it.label}</a>`).join('')}
    </div>

    <button class="mobile-toggle w-full flex items-center justify-between font-heading text-sm font-semibold text-foreground py-3" data-target="sub-admision">
      Admisión <span class="mobile-caret">${caretSVG}</span>
    </button>
    <div id="sub-admision" class="mobile-sub pl-4 space-y-1">
      ${admisionItems.map(it => `<a href="${BASE}pages/${it.href}" class="block text-sm text-muted-foreground py-2 hover:text-primary">${it.label}</a>`).join('')}
    </div>

    <a href="${BASE}pages/trabaja-con-nosotros.html" class="block font-heading text-sm font-semibold text-foreground py-3">Trabaja con nosotros</a>
    <a href="${BASE}pages/noticias.html" class="block font-heading text-sm font-semibold text-foreground py-3">Noticias</a>

    <a href="${BASE}index.html#admision" class="block bg-secondary text-secondary-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg text-center mt-3">Admisión 2026</a>
    <a href="https://intranet.catolicaschool.edu.pe" target="_blank" rel="noopener" class="block bg-accent text-accent-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg text-center mt-2">Intranet</a>
  </div>
</nav>`;

/* ----- Footer ----- */
const FooterHTML = `
<footer class="bg-primary text-white/80 py-14">
  <div class="container mx-auto px-4">
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10">
      <div>
        <img src="${BASE}assets/logo-catolica.png" alt="Católica School" class="h-16 object-contain mb-4" style="filter:brightness(0) invert(1);">
        <p class="text-sm leading-relaxed text-white/60">Formando líderes con valores, excelencia académica y visión global desde hace más de 20 años.</p>
      </div>
      <div>
        <h4 class="font-heading font-bold text-white mb-4">Enlaces</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="${BASE}index.html" class="hover:text-accent transition-colors">Inicio</a></li>
          <li><a href="${BASE}pages/quienes-somos.html" class="hover:text-accent transition-colors">¿Quiénes somos?</a></li>
          <li><a href="${BASE}pages/proceso-admision.html" class="hover:text-accent transition-colors">Admisión</a></li>
          <li><a href="${BASE}pages/noticias.html" class="hover:text-accent transition-colors">Noticias</a></li>
          <li><a href="${BASE}pages/trabaja-con-nosotros.html" class="hover:text-accent transition-colors">Trabaja con nosotros</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-heading font-bold text-white mb-4">Contacto</h4>
        <ul class="space-y-3 text-sm">
          <li class="flex items-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Av. Ejemplo 123, Lima, Perú
          </li>
          <li class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            906 026 820
          </li>
          <li class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            info@catolicaschool.edu.pe
          </li>
        </ul>
      </div>
      <div>
        <h4 class="font-heading font-bold text-white mb-4">Ubicación</h4>
        <div class="rounded-xl overflow-hidden border border-white/10">
          <iframe title="Ubicación Católica School"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3903.123!2d-77.0428!3d-11.8500!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTHCsDUxJzAwLjAiUyA3N8KwMDInMzQuMSJX!5e0!3m2!1ses!2spe!4v1"
            width="100%" height="180" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
    <div class="mt-10 pt-6 border-t border-white/10 text-center text-xs text-white/40">
      © 2026 Católica School. Todos los derechos reservados.
    </div>
  </div>
</footer>`;

/* ----- WhatsApp Button ----- */
const WhatsAppHTML = `
<a href="https://wa.me/51906026820" target="_blank" rel="noopener" aria-label="Contactar por WhatsApp"
   class="fixed bottom-6 right-6 z-50 w-16 h-16 bg-whatsapp text-whatsapp-foreground rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition-transform animate-float">
  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
       stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
  </svg>
</a>`;

/* ----- Social Sidebar ----- */
const SocialSidebarHTML = `
<div class="fixed right-0 top-1/2 -translate-y-1/2 z-40 hidden md:flex flex-col gap-0.5">
  <a href="#" aria-label="Instagram" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors rounded-tl-lg">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
  </a>
  <a href="#" aria-label="Facebook" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
  </a>
  <a href="#" aria-label="TikTok" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.15v-3.44a4.85 4.85 0 01-3.77-1.25V6.69z"/></svg>
  </a>
  <a href="#" aria-label="LinkedIn" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors rounded-bl-lg">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 6a2 2 0 100-4 2 2 0 000 4z"/></svg>
  </a>
</div>`;

/* ----- Inyección ----- */
function injectPartials() {
  const slot = (id, html) => {
    const el = document.getElementById(id);
    if (el) el.innerHTML = html;
  };
  slot('partial-banner',  TopBannerHTML);
  slot('partial-navbar',  NavbarHTML);
  slot('partial-footer',  FooterHTML);
  slot('partial-whatsapp', WhatsAppHTML);
  slot('partial-social',  SocialSidebarHTML);
}

document.addEventListener('DOMContentLoaded', () => {
  injectPartials();
  if (window.initInteractions) window.initInteractions();
});
