// <sp-contact>: el cierre "Let's create something", con el mismo fondo que la portada.
class SpContact extends HTMLElement {
  set data(p) { this._p = p; }

  connectedCallback() {
    const p = this._p;
    const mail = `mailto:${p.email}?subject=${encodeURIComponent("Let's create something")}`;
    this.attachShadow({ mode: 'open' }).innerHTML = `
      <style>
        :host { display: block; position: relative; overflow: hidden; margin-top: clamp(96px, 12vw, 180px);
                background: url(/html/selena/img/fondo.webp) center / cover; }
        .in { position: relative; padding: clamp(80px, 12vw, 160px) clamp(24px, 17.5vw, 260px); }
        h2 { margin: 0; font-weight: 900; font-size: clamp(2.2rem, 5.6vw, 4.6rem); line-height: 1; }
        .who { margin: 14px 0 0; font-weight: 300; font-size: clamp(1.1rem, 1.8vw, 1.4rem); }
        h3 { margin: 44px 0 10px; font-size: .78rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .chips span { padding: 6px 13px; border-radius: 99px; background: rgba(255,255,255,.5); font-size: .9rem; }
        .cta { display: flex; flex-wrap: wrap; gap: 12px; }
        .cta a { display: inline-flex; align-items: center; gap: 10px; padding: 14px 24px; border-radius: 99px;
                 background: var(--ink); color: #fff; text-decoration: none; font-weight: 700; transition: transform .25s, background .25s; }
        .cta a.alt { background: transparent; color: var(--ink); border: 1.5px solid var(--ink); }
        .cta a:hover { transform: translateY(-2px); }
        .cta a.alt:hover { background: var(--ink); color: #fff; }
        @media (max-width: 760px) { .in { padding: 88px 24px; } h3 { margin-top: 34px; } .cta { flex-direction: column; } .cta a { justify-content: center; } }
        footer { position: relative; padding: 18px clamp(24px, 17.5vw, 260px); font-size: .75rem; color: var(--ink-soft);
                 border-top: 1px dotted rgba(29,29,27,.25); }
      </style>
      <div class="in">
        <h2>LET’S CREATE<br>SOMETHING</h2>
        <p class="who">${p.name} · ${p.role}</p>
        <h3>Areas of expertise</h3>
        <div class="chips">${p.areas.map((a) => `<span>${a}</span>`).join('')}</div>
        <h3>Tools</h3>
        <div class="chips">${p.tools.map((t) => `<span>${t}</span>`).join('')}</div>
        <h3>Contact</h3>
        <div class="cta">
          <a href="${mail}">✉ ${p.email}</a>
          <a class="alt" href="${p.linkedin}" target="_blank" rel="noopener">LinkedIn ↗</a>
        </div>
      </div>
      <footer>© ${new Date().getFullYear()} ${p.name}</footer>`;
  }
}

customElements.define('sp-contact', SpContact);
