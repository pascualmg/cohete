// <sp-lightbox>: visor a pantalla completa para imágenes (con zoom) y vídeos.
class SpLightbox extends HTMLElement {
  connectedCallback() {
    const root = this.attachShadow({ mode: 'open' });
    root.innerHTML = `
      <style>
        :host { position: fixed; inset: 0; z-index: 100; display: none; }
        :host([open]) { display: block; }
        .bg { position: absolute; inset: 0; background: rgba(20,20,18,.94); animation: fade .3s; }
        @keyframes fade { from { opacity: 0 } }
        .stage { position: absolute; inset: 0; overflow: auto; display: grid; place-items: center; padding: 56px 16px 16px; }
        img { max-width: 100%; max-height: calc(100svh - 72px); cursor: zoom-in; animation: pop .35s cubic-bezier(.2,.7,.2,1); }
        .zoomed { place-items: start; }
        .zoomed img { max-width: none; max-height: none; width: 220%; cursor: zoom-out; }
        @media (min-width: 1300px) { .zoomed img { width: auto; } }
        @keyframes pop { from { opacity: 0; transform: scale(.97) } }
        .video { width: min(1200px, 100%); aspect-ratio: 16 / 9; }
        iframe { width: 100%; height: 100%; border: 0; border-radius: 6px; }
        button { position: fixed; top: 12px; right: 14px; z-index: 2; all: unset; position: fixed; top: 10px; right: 16px;
                 color: #fff; font-size: 2rem; line-height: 1; cursor: pointer; padding: 6px 10px; }
        .hint { position: fixed; top: 18px; left: 18px; color: rgba(255,255,255,.6); font: .8rem var(--font); }
      </style>
      <div class="bg"></div>
      <div class="stage"></div>
      <span class="hint"></span>
      <button aria-label="Cerrar">×</button>`;
    this.stage = root.querySelector('.stage');
    this.hint = root.querySelector('.hint');
    root.querySelector('button').addEventListener('click', () => this.close());
    this.stage.addEventListener('click', (e) => {
      if (e.target === this.stage) return this.close();
      if (e.target.tagName === 'IMG') this.stage.classList.toggle('zoomed');
    });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') this.close(); });
  }

  open(d) {
    this.stage.classList.remove('zoomed');
    if (d.type === 'youtube') {
      this.stage.innerHTML = `<div class="video"><iframe src="https://www.youtube-nocookie.com/embed/${d.id}?autoplay=1&rel=0"
        allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen title="Vídeo"></iframe></div>`;
      this.hint.textContent = '';
    } else {
      this.stage.innerHTML = `<img src="${d.src}" alt="${d.alt}">`;
      this.hint.textContent = 'Pulsa la imagen para ampliar';
    }
    this.setAttribute('open', '');
    document.body.style.overflow = 'hidden';
  }

  close() {
    if (!this.hasAttribute('open')) return;
    this.removeAttribute('open');
    this.stage.innerHTML = '';   // corta el vídeo al cerrar
    document.body.style.overflow = '';
  }
}

customElements.define('sp-lightbox', SpLightbox);
