// <sp-project>: un trabajo. Texto vivo + la parte visual + enlaces.
// - Escritorio: la composición entera, tal cual la maquetó Selena.
// - Móvil: la composición partida en sus piezas (foto + pie), una debajo de otra;
//   las piezas apaisadas van en panorámica, deslizando con el dedo.
// Pulsar cualquier imagen la abre a pantalla completa.
const IMG = '/html/selena/img/';

class SpProject extends HTMLElement {
  set data(p) { this._p = p; }

  connectedCallback() {
    const p = this._p;
    const head = p.kicker || p.title || p.subtitle || p.text;
    const tiles = p.tiles || [];
    const root = this.attachShadow({ mode: 'open' });

    const tile = ([name, w, h]) => w / h > 1.3
      ? `<div class="pano"><button class="t" data-src="${IMG}t/${name}.webp"><img style="height: min(62vh, 360px, ${(200 * h / w).toFixed(1)}vw)" src="${IMG}t/${name}.webp" width="${w}" height="${h}" alt="${p.alt}" loading="lazy" decoding="async"></button><span class="swipe">desliza →</span></div>`
      : `<button class="t${w / h < 0.8 ? ' narrow' : ''}" data-src="${IMG}t/${name}.webp"><img src="${IMG}t/${name}.webp" width="${w}" height="${h}" alt="${p.alt}" loading="lazy" decoding="async"></button>`;

    root.innerHTML = `
      <style>
        :host { display: block; margin-top: clamp(48px, 6vw, 80px); }
        .kicker { margin: 0; font-weight: 800; font-size: 1.15rem; }
        .title { margin: 4px 0 0; font-weight: 600; text-transform: uppercase; font-size: .9rem; letter-spacing: .02em; }
        .sub { margin: 6px 0 0; font-size: .9rem; color: var(--ink-soft); }
        .text { margin: 12px 0 0; max-width: 72ch; line-height: 1.6; }
        button { all: unset; display: block; cursor: zoom-in; position: relative; }
        button:focus-visible { outline: 2px solid var(--link); outline-offset: 4px; }
        img { display: block; width: 100%; height: auto; }

        .board { margin-top: ${head ? '24px' : '0'}; border-radius: 4px; overflow: hidden; width: 100%; }
        .board img { transition: transform 1.2s cubic-bezier(.2,.7,.2,1); }
        .board:hover img { transform: scale(1.012); }
        .zoom { position: absolute; right: 14px; bottom: 14px; background: rgba(29,29,27,.78); color: #fff; font-size: .75rem;
                padding: 7px 12px; border-radius: 99px; letter-spacing: .06em; opacity: 0; transition: opacity .3s; }
        .board:hover .zoom, .board:focus-visible .zoom { opacity: 1; }

        .tiles { display: none; }
        @media (max-width: 760px) {
          :host { margin-top: 56px; }
          .has-tiles .board { display: none; }
          .has-tiles .tiles { display: grid; grid-template-columns: 1fr 1fr; grid-auto-flow: dense; gap: 18px 12px;
                             align-items: start; margin-top: ${head ? '20px' : '0'}; }
          .tiles > * { grid-column: 1 / -1; }
          .tiles > .narrow { grid-column: auto; }
          .t img { border-radius: 3px; }
          .pano { position: relative; margin: 0 -16px; overflow-x: auto; overscroll-behavior-x: contain;
                  scrollbar-width: none; -webkit-overflow-scrolling: touch; }
          .pano::-webkit-scrollbar { display: none; }
          .pano .t { width: max-content; padding: 0 16px; }
          .pano img { height: min(62vh, 360px); width: auto; max-width: none; }
          .swipe { position: sticky; left: calc(100% - 96px); bottom: 10px; display: inline-block; margin-top: -40px;
                   background: rgba(29,29,27,.72); color: #fff; font-size: .72rem; padding: 6px 11px; border-radius: 99px;
                   pointer-events: none; transition: opacity .4s; }
          .pano.moved .swipe { opacity: 0; }
          .links a { flex: 1 1 auto; justify-content: center; }
        }

        .links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        .links a { display: inline-flex; align-items: center; gap: 8px; padding: 11px 18px; border: 1px solid var(--ink);
                   border-radius: 99px; color: var(--ink); text-decoration: none; font-size: .9rem; font-weight: 600;
                   transition: background .25s, color .25s; cursor: pointer; }
        .links a:hover { background: var(--ink); color: #fff; }
        .reveal { opacity: 0; transform: translateY(32px); transition: opacity 1s, transform 1s cubic-bezier(.2,.7,.2,1); }
        .reveal.in { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1; transform: none; transition: none; } }
      </style>
      <div class="wrap ${tiles.length ? 'has-tiles' : ''}">
        <div class="reveal">
          ${p.kicker ? `<p class="kicker">${p.kicker}</p>` : ''}
          ${p.title ? `<p class="title">${p.title}</p>` : ''}
          ${p.subtitle ? `<p class="sub">${p.subtitle}</p>` : ''}
          ${p.text ? `<p class="text">${p.text}</p>` : ''}
        </div>
        <button class="board reveal" data-src="${IMG}${p.image}.webp" aria-label="Ampliar: ${p.alt}">
          <img src="${IMG}${p.image}.webp" alt="${p.alt}" loading="lazy" decoding="async">
          <span class="zoom">AMPLIAR ⤢</span>
        </button>
        <div class="tiles">${tiles.map(tile).join('')}</div>
        ${p.links ? `<div class="links reveal">${p.links.map((l) => l.youtube
            ? `<a data-yt="${l.youtube}" role="button" tabindex="0">▶ ${l.label}</a>`
            : `<a href="${l.url}" target="_blank" rel="noopener">${l.label} ↗</a>`).join('')}</div>` : ''}
      </div>`;

    const open = (detail) => this.dispatchEvent(new CustomEvent('sp-open', { detail, bubbles: true, composed: true }));
    root.querySelectorAll('button[data-src]').forEach((b) =>
      b.addEventListener('click', () => open({ type: 'image', src: b.dataset.src, alt: p.alt })));
    root.querySelectorAll('[data-yt]').forEach((a) => {
      const go = () => open({ type: 'youtube', id: a.dataset.yt });
      a.addEventListener('click', go);
      a.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); } });
    });
    root.querySelectorAll('.pano').forEach((el) =>
      el.addEventListener('scroll', () => el.classList.add('moved'), { once: true, passive: true }));

    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    }), { threshold: 0.08 });
    root.querySelectorAll('.reveal').forEach((el) => io.observe(el));
  }
}

customElements.define('sp-project', SpProject);
