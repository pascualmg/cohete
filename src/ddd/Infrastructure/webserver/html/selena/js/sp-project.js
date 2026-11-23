// <sp-project>: un trabajo. Texto vivo + la composición visual + enlaces.
// Pulsar la imagen la abre a pantalla completa; los vídeos de YouTube se ven aquí mismo.
const IMG = '/html/selena/img/';

class SpProject extends HTMLElement {
  set data(p) { this._p = p; }

  connectedCallback() {
    const p = this._p;
    const head = p.kicker || p.title || p.subtitle || p.text;
    const root = this.attachShadow({ mode: 'open' });
    root.innerHTML = `
      <style>
        :host { display: block; margin-top: clamp(40px, 6vw, 80px); }
        .kicker { margin: 0; font-weight: 800; font-size: 1.15rem; }
        .title { margin: 4px 0 0; font-weight: 600; text-transform: uppercase; font-size: .9rem; letter-spacing: .02em; }
        .sub { margin: 6px 0 0; font-size: .9rem; color: var(--ink-soft); }
        .text { margin: 12px 0 0; max-width: 72ch; line-height: 1.6; }
        button.img { all: unset; display: block; margin-top: ${head ? '24px' : '0'}; cursor: zoom-in; position: relative;
                     border-radius: 4px; overflow: hidden; width: 100%; }
        img { display: block; width: 100%; height: auto; transition: transform 1.2s cubic-bezier(.2,.7,.2,1); }
        button.img:hover img { transform: scale(1.012); }
        .zoom { position: absolute; right: 14px; bottom: 14px; background: rgba(29,29,27,.78); color: #fff; font-size: .75rem;
                padding: 7px 12px; border-radius: 99px; letter-spacing: .06em; opacity: 0; transition: opacity .3s; }
        button.img:hover .zoom, button.img:focus-visible .zoom { opacity: 1; }
        @media (hover: none) { .zoom { opacity: 1; } }
        button.img:focus-visible { outline: 2px solid var(--link); outline-offset: 4px; }
        .links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        .links a { display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px; border: 1px solid var(--ink);
                   border-radius: 99px; color: var(--ink); text-decoration: none; font-size: .88rem; font-weight: 600;
                   transition: background .25s, color .25s; cursor: pointer; }
        .links a:hover { background: var(--ink); color: #fff; }
        .reveal { opacity: 0; transform: translateY(32px); transition: opacity 1s, transform 1s cubic-bezier(.2,.7,.2,1); }
        .reveal.in { opacity: 1; transform: none; }
      </style>
      <div class="reveal">
        ${p.kicker ? `<p class="kicker">${p.kicker}</p>` : ''}
        ${p.title ? `<p class="title">${p.title}</p>` : ''}
        ${p.subtitle ? `<p class="sub">${p.subtitle}</p>` : ''}
        ${p.text ? `<p class="text">${p.text}</p>` : ''}
      </div>
      <button class="img reveal" aria-label="Ampliar: ${p.alt}">
        <picture>
          <source media="(max-width: 900px)" srcset="${IMG}${p.image}-m.webp">
          <img src="${IMG}${p.image}.webp" alt="${p.alt}" loading="lazy" decoding="async">
        </picture>
        <span class="zoom">AMPLIAR ⤢</span>
      </button>
      ${p.links ? `<div class="links reveal">${p.links.map((l) => l.youtube
          ? `<a data-yt="${l.youtube}" role="button" tabindex="0">▶ ${l.label}</a>`
          : `<a href="${l.url}" target="_blank" rel="noopener">${l.label} ↗</a>`).join('')}</div>` : ''}`;

    const open = (detail) => this.dispatchEvent(new CustomEvent('sp-open', { detail, bubbles: true, composed: true }));
    root.querySelector('button.img').addEventListener('click', () =>
      open({ type: 'image', src: `${IMG}${p.image}.webp`, alt: p.alt }));
    root.querySelectorAll('[data-yt]').forEach((a) => {
      const go = () => open({ type: 'youtube', id: a.dataset.yt });
      a.addEventListener('click', go);
      a.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); } });
    });

    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    }), { threshold: 0.08 });
    root.querySelectorAll('.reveal').forEach((el) => io.observe(el));
  }
}

customElements.define('sp-project', SpProject);
