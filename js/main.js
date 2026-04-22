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
    // Disable native HTML5 validation so we can show our own alerts
    form.setAttribute('novalidate', 'true');
    
    // Ocultar mensajes de error al escribir o cambiar una opción y filtrar caracteres
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
      const hideError = () => {
        const errorEl = document.getElementById(`err-${input.name}`);
        if (errorEl) {
          errorEl.classList.add('hidden');
        }
      };
      input.addEventListener('input', () => {
        // Solo aceptar números en teléfono y DNI
        if (input.name === 'telefono' || input.name === 'dni') {
          input.value = input.value.replace(/\D/g, '');
        }
        hideError();
      });
      input.addEventListener('change', hideError);
    });

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      
      const formData = new FormData(form);
      const nombre = formData.get('nombre')?.trim();
      const correo = formData.get('correo')?.trim();
      const telefono = formData.get('telefono')?.trim();
      const dni = formData.get('dni')?.trim();
      const nivel = formData.get('nivel');
      const consentimiento = form.querySelector('input[name="consentimiento"]').checked;
      
      // Ocultar todos los mensajes de error primero
      document.getElementById('err-nombre').classList.add('hidden');
      document.getElementById('err-correo').classList.add('hidden');
      document.getElementById('err-telefono').classList.add('hidden');
      document.getElementById('err-dni').classList.add('hidden');
      document.getElementById('err-nivel').classList.add('hidden');
      document.getElementById('err-consentimiento').classList.add('hidden');

      let isValid = true;

      // Validación de Nombre
      if (!nombre) {
        document.getElementById('err-nombre').classList.remove('hidden');
        isValid = false;
      }

      // Validación de Correo
      if (!correo) {
        const errEl = document.getElementById('err-correo');
        errEl.textContent = 'Por favor ingresa un correo electrónico.';
        errEl.classList.remove('hidden');
        isValid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        const errEl = document.getElementById('err-correo');
        errEl.textContent = 'El formato del correo es inválido.';
        errEl.classList.remove('hidden');
        isValid = false;
      }

      // Validación de Teléfono (solo números, mínimo 9 dígitos)
      if (!telefono) {
        const errEl = document.getElementById('err-telefono');
        errEl.textContent = 'Por favor ingresa tu teléfono.';
        errEl.classList.remove('hidden');
        isValid = false;
      } else if (telefono.length < 9) {
        const errEl = document.getElementById('err-telefono');
        errEl.textContent = 'El teléfono debe tener al menos 9 dígitos.';
        errEl.classList.remove('hidden');
        isValid = false;
      }

      // Validación de DNI/Documento (solo números, 8 a 12 dígitos)
      if (!dni) {
        const errEl = document.getElementById('err-dni');
        errEl.textContent = 'Por favor ingresa tu documento.';
        errEl.classList.remove('hidden');
        isValid = false;
      } else if (dni.length < 8) {
        const errEl = document.getElementById('err-dni');
        errEl.textContent = 'El documento debe tener al menos 8 dígitos.';
        errEl.classList.remove('hidden');
        isValid = false;
      }

      // Validación de Nivel
      if (!nivel) {
        document.getElementById('err-nivel').classList.remove('hidden');
        isValid = false;
      }

      // Validación de Consentimiento
      if (!consentimiento) {
        document.getElementById('err-consentimiento').classList.remove('hidden');
        isValid = false;
      }

      // Si hay algún error, detenemos el envío
      if (!isValid) {
        return;
      }
      
      // Mostrar estado de carga en el botón
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Enviando...';
      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-70');

      fetch('./procesar_correo.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Mostrar notificación de éxito
          const toast = document.getElementById('toast-success');
          if (toast) {
            toast.classList.remove('opacity-0', 'translate-y-10', 'pointer-events-none');
            toast.classList.add('opacity-100', 'translate-y-0');
            setTimeout(() => {
              toast.classList.remove('opacity-100', 'translate-y-0');
              toast.classList.add('opacity-0', 'translate-y-10', 'pointer-events-none');
            }, 5000);
          } else {
            alert('¡Gracias por registrarte! Nos pondremos en contacto contigo pronto.');
          }
          // Limpiar formulario
          form.reset();
        } else {
          // Hubo un error devuelto por PHP (ej. falló el SMTP)
          alert('Hubo un problema enviando el mensaje: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al intentar conectarse al servidor. Por favor, inténtalo de nuevo.');
      })
      .finally(() => {
        // Restaurar el botón siempre
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-70');
      });
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
