/* --- PRELOADER SCRIPT --- */
(function() {
  const BASE = window.location.pathname.includes('/pages/') ? '../' : './';
  
  // Agregar CSS del preloader inmediatamente
  const style = document.createElement('style');
  style.innerHTML = `
    #loader-overlay {
      position: fixed;
      inset: 0;
      z-index: 999999;
      background-color: hsl(210, 14%, 95%); /* background color del theme */
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 1;
      visibility: visible;
      transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
    }
    .loader-logo {
      height: 7rem;
      object-fit: contain;
      animation: preloaderPulse 1.5s ease-in-out infinite;
    }
    @keyframes preloaderPulse {
      0%, 100% { transform: scale(1); opacity: 1; filter: drop-shadow(0 0 10px rgba(0,0,0,0.1)); }
      50% { transform: scale(1.1); opacity: 0.8; filter: drop-shadow(0 0 20px rgba(0,0,0,0.2)); }
    }
    body.loading {
      overflow: hidden;
    }
  `;
  document.head.appendChild(style);

  // Insertar el preloader tan pronto como el body exista
  const observer = new MutationObserver((mutations, obs) => {
    if (document.body) {
      document.body.classList.add('loading');
      document.body.insertAdjacentHTML('afterbegin', `
        <div id="loader-overlay">
          <img src="${BASE}assets/icono.png" class="loader-logo" alt="Cargando Católica School...">
        </div>
      `);
      obs.disconnect();

      // Ocultar preloader después de exactamente 2 segundos
      setTimeout(() => {
        const loader = document.getElementById('loader-overlay');
        if (loader) {
          loader.style.opacity = '0';
          loader.style.visibility = 'hidden';
          setTimeout(() => {
            loader.remove();
            document.body.classList.remove('loading');
          }, 500); // 500ms for exit transition
        }
      }, 2000);
    }
  });
  observer.observe(document.documentElement, { childList: true });
})();
