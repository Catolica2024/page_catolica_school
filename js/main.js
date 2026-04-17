/* ===========================================================
   Interacciones JS puras: navbar (dropdowns desktop con delay,
   menú móvil con submenús), banner cerrar, FAQ accordion,
   formulario, scroll reveal con IntersectionObserver.
   =========================================================== */

window.initInteractions = function () {
  /* ---------- Top banner cerrar ---------- */
  const closeBtn = document.getElementById('top-banner-close');
  const banner   = document.getElementById('top-banner');
  if (closeBtn && banner) {
    closeBtn.addEventListener('click', () => banner.remove());
  }

  /* ---------- Navbar móvil burger ---------- */
  const burger     = document.getElementById('burger');
  const mobileMenu = document.getElementById('mobile-menu');
  const iconMenu   = document.getElementById('icon-menu');
  const iconClose  = document.getElementById('icon-close');
  if (burger && mobileMenu) {
    burger.addEventListener('click', () => {
      const isOpen = !mobileMenu.classList.contains('hidden');
      mobileMenu.classList.toggle('hidden');
      iconMenu.classList.toggle('hidden', !isOpen ? true : false);
      iconClose.classList.toggle('hidden', !isOpen ? false : true);
    });
  }

  /* ---------- Submenús móviles ---------- */
  document.querySelectorAll('.mobile-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);
      const caret  = btn.querySelector('.mobile-caret');
      if (!target) return;
      target.classList.toggle('open');
      caret && caret.classList.toggle('open');
    });
  });

  /* ---------- Dropdowns desktop con delay 150ms ---------- */
  document.querySelectorAll('[data-dropdown]').forEach(item => {
    const panel = item.querySelector('.nav-dropdown');
    const caret = item.querySelector('.nav-caret');
    let timeout;
    const open  = () => { clearTimeout(timeout); panel.classList.add('open'); caret && caret.classList.add('open'); };
    const close = () => { timeout = setTimeout(() => { panel.classList.remove('open'); caret && caret.classList.remove('open'); }, 150); };
    item.addEventListener('mouseenter', open);
    item.addEventListener('mouseleave', close);
    // Soporte teclado (focus)
    item.querySelector('button')?.addEventListener('focus', open);
    item.addEventListener('focusout', (e) => {
      if (!item.contains(e.relatedTarget)) close();
    });
  });

  /* ---------- FAQ Accordion ---------- */
  document.querySelectorAll('.faq-item').forEach(item => {
    const trigger = item.querySelector('.faq-trigger');
    const content = item.querySelector('.faq-content');
    if (!trigger || !content) return;
    trigger.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      // Cerrar todos
      document.querySelectorAll('.faq-item.open').forEach(other => {
        other.classList.remove('open');
        const c = other.querySelector('.faq-content');
        if (c) c.style.maxHeight = '0px';
      });
      if (!isOpen) {
        item.classList.add('open');
        content.style.maxHeight = content.scrollHeight + 'px';
      }
    });
  });

  /* ---------- Formulario de registro ---------- */
  const form = document.getElementById('registration-form');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const data = Object.fromEntries(new FormData(form).entries());
      console.log('Registro:', data);
      alert('¡Gracias por registrarte! Nos pondremos en contacto contigo pronto.');
      form.reset();
    });
  }

  /* ---------- Scroll reveal ---------- */
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
};
