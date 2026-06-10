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
    ¡Trasládate a <strong>Católica School</strong>!
    <a href="https://wa.me/51906026820?text=Hola,%20quiero%20más%20información%20sobre%20los%20traslados" target="_blank" rel="noopener" class="underline font-bold hover:opacity-80 transition-opacity">Recibe más información aquí</a>
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
  { label: '¿Quiénes somos?', href: 'quienes-somos.html' },
  { label: 'Propuesta pedagógica', href: 'propuesta-pedagogica.html' },
  { label: 'Documentos', href: 'reglamentos.html' },
  { label: 'Nuestra Infraestructura', href: 'infraestructura.html' },
];
const admisionItems = [
  { label: 'Proceso de admisión', href: 'proceso-admision.html' },
  { label: 'Traslados', href: 'traslados.html' },
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
      <li><a href="${BASE}pages/noticias.php" class="font-heading text-sm font-semibold text-foreground hover:text-primary transition-colors">Noticias</a></li>
    </ul>

    <div class="hidden lg:flex items-center gap-3">
      <a href="${BASE}index.html#admision"
         class="bg-secondary text-secondary-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
        Admisión 2027
      </a>
      <a href="https://www.peruschool.edu.pe/catolicaschool" target="_blank" rel="noopener"
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
    <a href="${BASE}pages/noticias.php" class="block font-heading text-sm font-semibold text-foreground py-3">Noticias</a>

    <a href="${BASE}index.html#admision" class="block bg-secondary text-secondary-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg text-center mt-3">Admisión 2027</a>
    <a href="https://www.peruschool.edu.pe/catolicaschool" target="_blank" rel="noopener" class="block bg-accent text-accent-foreground font-heading font-bold text-sm px-6 py-2.5 rounded-lg text-center mt-2">Intranet</a>
  </div>
</nav>`;

/* ----- Footer ----- */
const FooterHTML = `
<footer class="bg-primary text-white/80 py-14">
  <div class="container mx-auto px-4">
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10">
      <div>
        <img src="${BASE}assets/lema_catolica.png" alt="Católica School" class="object-contain mb-4" style="width: 100px; filter:brightness(0) invert(1);">
        <p class="text-sm leading-relaxed text-white/60">Formando líderes con valores, excelencia académica y visión global desde hace más de 20 años.</p>
      </div>
      <div>
        <h4 class="font-heading font-bold text-white mb-4">Enlaces</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="${BASE}index.html" class="hover:text-accent transition-colors">Inicio</a></li>
          <li><a href="${BASE}pages/quienes-somos.html" class="hover:text-accent transition-colors">¿Quiénes somos?</a></li>
          <li><a href="${BASE}pages/proceso-admision.html" class="hover:text-accent transition-colors">Admisión</a></li>
          <li><a href="${BASE}pages/noticias.php" class="hover:text-accent transition-colors">Noticias</a></li>
          <li><a href="${BASE}pages/trabaja-con-nosotros.html" class="hover:text-accent transition-colors">Trabaja con nosotros</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-heading font-bold text-white mb-4">Contacto</h4>
        <ul class="space-y-3 text-sm">
          <li class="flex items-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent mt-0.5 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Calle W, Mz Q2 Séptima Etapa-Santo Domingo de Carabayllo, Carabayllo, Peru
          </li>
          <li class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            906 026 820
          </li>
          <li class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-accent shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            mesadepartes@colegiolacatolica.edu.pe
          </li>
        </ul>
      </div>
      <div>
        <h4 class="font-heading font-bold text-white mb-4">Ubicación</h4>
        <div class="rounded-xl overflow-hidden border border-white/10">
          <iframe title="Ubicación Católica School"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3904.3896595306455!2d-77.03401242405351!3d-11.87793223878208!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105d0b8c515cd2f%3A0x72d08cfea0969124!2sCat%C3%B3lica%20School!5e0!3m2!1ses-419!2spe!4v1777991324244!5m2!1ses-419!2spe"
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
<a href="https://wa.me/51906026820?text=Hola,%20vengo%20de%20la%20p%C3%A1gina%20web%20de%20Cat%C3%B3lica%20School" target="_blank" rel="noopener" aria-label="Contactar por WhatsApp"
   class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-whatsapp text-whatsapp-foreground rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition-transform animate-float">
  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
    <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
  </svg>
</a>`;

/* ----- Social Sidebar ----- */
const SocialSidebarHTML = `
<div class="fixed right-0 top-1/2 -translate-y-1/2 z-40 hidden md:flex flex-col gap-0.5">
  <a href="https://www.instagram.com/catolicaschool/" target="_blank" rel="noopener" aria-label="Instagram" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors rounded-tl-lg">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
  </a>
  <a href="https://www.facebook.com/CatolicaSchool/" target="_blank" rel="noopener" aria-label="Facebook" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
  </a>
  <a href="https://www.tiktok.com/@catolicaschool" target="_blank" rel="noopener" aria-label="TikTok" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.15v-3.44a4.85 4.85 0 01-3.77-1.25V6.69z"/></svg>
  </a>
  <a href="https://pe.linkedin.com/company/catolica-school" target="_blank" rel="noopener" aria-label="LinkedIn" class="w-12 h-12 bg-primary text-primary-foreground flex items-center justify-center hover:bg-secondary transition-colors rounded-bl-lg">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 6a2 2 0 100-4 2 2 0 000 4z"/></svg>
  </a>
</div>`;

/* ----- FAQ Chat ----- */
const FaqChatHTML = `
<div id="faq-chat-container" class="faq-fixed faq-bottom-24 faq-right-6 faq-flex faq-flex-col faq-items-end" style="z-index: 9999;">
  <!-- Botón Flotante FAQ -->
  <button id="faq-chat-trigger" aria-label="Preguntas frecuentes"
          class="faq-w-14 faq-h-14 faq-bg-primary faq-text-primary-foreground faq-rounded-full faq-flex faq-items-center faq-justify-center faq-shadow-xl faq-hover-scale-110 faq-transition-transform faq-animate-float faq-mb-2" style="z-index: 10000;">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
      <line x1="12" y1="17" x2="12.01" y2="17"></line>
    </svg>
  </button>

  <!-- Ventana de Chat -->
  <div id="faq-chat-window" class="faq-hidden faq-w-[350px] faq-max-w-[90vw] faq-h-[500px] faq-bg-card faq-rounded-2xl faq-shadow-2xl faq-flex faq-flex-col faq-overflow-hidden faq-border faq-border-border">
    <!-- Header -->
    <div class="faq-bg-primary faq-p-4 faq-flex faq-items-center faq-justify-between faq-text-primary-foreground">
      <div class="faq-flex faq-items-center faq-gap-3">
        <div class="faq-w-10 faq-h-10 faq-rounded-full faq-bg-white-20 faq-flex faq-items-center faq-justify-center">
          <img src="${BASE}assets/icono.png" class="faq-w-6 faq-h-6 logo-white" alt="Logo">
        </div>
        <div>
          <h4 class="faq-font-heading faq-font-bold faq-text-sm">Católica School</h4>
          <span class="faq-text-[10px] faq-opacity-80 faq-flex faq-items-center faq-gap-1">
            <span class="faq-w-1.5 faq-h-1.5 faq-bg-green-400 faq-rounded-full"></span> Respuesta instantánea
          </span>
        </div>
      </div>
      <button id="faq-chat-close" class="faq-hover-bg-white-10 faq-p-1 faq-rounded-lg faq-transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <!-- Body -->
    <div id="faq-chat-body" class="faq-flex-1 faq-overflow-y-auto faq-p-4 faq-space-y-4 faq-bg-muted-30">
      <div class="chat-msg system faq-bg-white faq-rounded-2xl faq-rounded-tl-none faq-p-3 faq-shadow-sm faq-text-sm faq-text-foreground faq-max-w-[85%] faq-border faq-border-border">
        ¡Hola! 👋 Soy el asistente virtual de Católica School. ¿En qué podemos ayudarte hoy? Selecciona una de las dudas más comunes:
      </div>
      
      <div id="faq-options" class="faq-flex faq-flex-col faq-gap-2">
        <!-- Opciones se inyectan aquí -->
      </div>
    </div>

    <!-- Footer -->
    <div class="faq-p-3 faq-border-t faq-bg-card faq-text-center">
      <a href="https://wa.me/51906026820" target="_blank" class="faq-text-primary faq-font-bold faq-text-xs faq-hover-underline faq-flex faq-items-center faq-justify-center faq-gap-1">
        ¿Aún tienes dudas? Habla con nosotros <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 22 3 22 10"></polyline><line x1="10" y1="14" x2="22" y2="2"></line></svg>
      </a>
    </div>
  </div>
</div>`;

const FAQ_DATA = [
  { 
    q: "¿Cuáles son los niveles educativos que ofrece el Colegio?", 
    a: "En Católica School contamos con tres niveles educativos:\n\n• Early Years: Inicial y 1.er grado\n• Elementary: de 2.º a 5.º grado de primaria\n• Middle & High School: de 6.º grado de primaria a 5.º de secundaria" 
  },
  { 
    q: "¿Cuál es el horario de clases?", 
    a: "Los horarios de clase se organizan por nivel educativo:\n\nInicial\n• 3, 4 y 5 años: de 7:45 a.m. a 1:15 p.m.\n\nPrimaria\n• 1er grado: de 7:30 a.m. a 2:20 p.m.\n• De 2do a 6to grado: de 7:30 a.m. a 2:20 p.m.\n\nSecundaria\n• De 1er a 5to año: de 7:30 a.m. a 3:00 p.m." 
  },
  { 
    q: "¿El Colegio cuenta con programa Bilingüe?", 
    a: "Contamos con un programa bilingüe en desarrollo. Actualmente, los estudiantes reciben 7 horas de inglés a la semana.\n\nEn el nivel de primaria (de 1.er a 6.º grado), dentro de esta carga horaria se incluyen 2 horas del curso de Science, el cual se desarrolla íntegramente en inglés. Estas sesiones están diseñadas como experiencias de aprendizaje 100% en este segundo idioma, promoviendo la comprensión, exploración y aplicación de contenidos de manera natural y contextualizada." 
  },
  { 
    q: "¿Cuáles son las actividades extracurriculares disponibles?", 
    a: "Nuestros estudiantes pueden acceder a los talleres extracurriculares a través de Católica Kids Club, donde ofrecen diversas opciones para complementar su desarrollo integral:\n\n• Gimnasia\n• Vóley\n• Ballet\n• Estimulación temprana y adaptación\n• Teatro\n• Natación" 
  },
  { 
    q: "¿Cómo es el proceso de admisión?", 
    a: "Nuestro proceso de admisión consta de 5 pasos:\n\n1. Solicitud de información\nComplete el formulario de interés o comuníquese con nuestra oficina de admisión para recibir orientación inicial.\n\n2. Visita guiada\nAgende una visita para conocer nuestras instalaciones, propuesta pedagógica y equipo docente.\n\n3. Entrega de documentos\nPresente la documentación requerida: DNI, libreta de notas, carta de no adeudo y constancia de conducta.\n\n4. Entrevista familiar\nLa familia participará en una entrevista que nos permitirá conocer mejor al estudiante y acompañar su proceso de admisión según la vacante a la que postula.\n\n5. Matrícula\nAl completar las etapas previas, se realiza la matrícula con el acompañamiento del equipo de admisión." 
  },
  { 
    q: "¿Ofrecen servicio de transporte escolar?", 
    a: "Actualmente, el colegio no brinda servicio de transporte escolar. Sin embargo, existen movilidades externas que ofrecen este servicio. Podemos compartir algunas referencias, previa solicitud." 
  },
  { 
    q: "¿Cuáles son las formas de pago?", 
    a: "Aceptamos pagos a través de tarjetas de débito y crédito, así como mediante Yape, Plin y pago en efectivo." 
  },
  { 
    q: "¿El colegio cuenta con servicio de alimentación?", 
    a: "Contamos con un concesionario dentro de la escuela, que brinda servicio de alimentación para nuestros estudiantes." 
  }
];

/* ----- Inyección ----- */
function injectPartials() {
  const slot = (id, html) => {
    const el = document.getElementById(id);
    if (el) el.innerHTML = html;
  };
  slot('partial-banner', TopBannerHTML);
  slot('partial-navbar', NavbarHTML);
  slot('partial-footer', FooterHTML);
  slot('partial-whatsapp', WhatsAppHTML);
  slot('partial-social', SocialSidebarHTML);

  // FAQ Chat Injection
  let faqContainer = document.getElementById('partial-faq');
  if (!faqContainer) {
    faqContainer = document.createElement('div');
    faqContainer.id = 'partial-faq';
    document.body.appendChild(faqContainer);
  }
  faqContainer.innerHTML = FaqChatHTML;
  initFaqChat();
}

function initFaqChat() {
  const trigger = document.getElementById('faq-chat-trigger');
  const windowEl = document.getElementById('faq-chat-window');
  const closeBtn = document.getElementById('faq-chat-close');
  const optionsEl = document.getElementById('faq-options');
  const bodyEl = document.getElementById('faq-chat-body');

  if (!trigger || !windowEl) return;
  console.log('FAQ Chat Initialized');

  const toggleChat = () => {
    windowEl.classList.toggle('faq-hidden');
  };

  trigger.addEventListener('click', toggleChat);
  closeBtn.addEventListener('click', toggleChat);

  const renderOptions = () => {
    optionsEl.innerHTML = FAQ_DATA.map((item, index) => `
      <button class="faq-opt-btn bg-white border border-primary/20 text-primary text-xs font-semibold py-2 px-3 rounded-xl hover:bg-primary hover:text-white transition-all text-left" data-index="${index}">
        ${item.q}
      </button>
    `).join('');

    document.querySelectorAll('.faq-opt-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const index = btn.dataset.index;
        const qa = FAQ_DATA[index];
        addMessage(qa.q, 'user');
        optionsEl.innerHTML = ''; // Limpiar opciones mientras responde
        
        setTimeout(() => {
          addMessage(qa.a, 'system');
          setTimeout(() => {
            renderOptions(); // Las opciones se cargan abajo
            
            // Agregar aviso discreto de que hay más preguntas abajo
            const hint = document.createElement('button');
            hint.className = "faq-text-primary faq-font-bold faq-flex faq-items-center faq-gap-1 faq-mx-auto faq-mt-2 faq-hover-underline faq-transition-colors faq-animate-fade-in";
            hint.style.fontSize = "10px";
            hint.style.opacity = "0.6";
            hint.style.margin = "0 auto";
            hint.innerHTML = `Ver más preguntas <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>`;
            hint.onclick = () => {
              bodyEl.scrollTo({ top: bodyEl.scrollHeight, behavior: 'smooth' });
              hint.remove(); // Quitar el aviso al bajar
            };
            bodyEl.insertBefore(hint, optionsEl);
          }, 400);
        }, 600);
      });
    });
  };

  const addMessage = (text, type) => {
    const msg = document.createElement('div');
    msg.className = `chat-msg ${type} faq-rounded-2xl faq-p-3 faq-shadow-sm faq-text-sm faq-max-w-[85%] faq-animate-fade-in faq-border faq-border-border`;
    if (type === 'user') {
      msg.className += ' faq-bg-primary faq-text-white faq-self-end faq-rounded-tr-none';
      msg.style.alignSelf = 'flex-end';
    } else {
      msg.className += ' faq-bg-white faq-text-foreground faq-self-start faq-rounded-tl-none';
      msg.style.alignSelf = 'flex-start';
    }
    msg.textContent = text;
    
    // Insertar ANTES de las opciones
    bodyEl.insertBefore(msg, optionsEl);
    
    setTimeout(() => {
      // Si es sistema, enfocamos el inicio del mensaje. Si es usuario, vamos al final.
      const targetScroll = type === 'system' ? msg.offsetTop - 10 : bodyEl.scrollHeight;
      bodyEl.scrollTo({ top: targetScroll, behavior: 'smooth' });
    }, 50);
  };

  renderOptions();
}

document.addEventListener('DOMContentLoaded', () => {
  injectPartials();
  if (window.initInteractions) window.initInteractions();
});
