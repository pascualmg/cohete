// <sp-nav visible>: barra fija con las secciones; marca en la que estás.
class SpNav extends HTMLElement {
  set data(sections) { this._s = sections; }

  connectedCallback() {
    const s = this._s;
    this.attachShadow({ mode: 'open' }).innerHTML = `
      <style>
        :host { position: fixed; inset: 0 0 auto 0; z-index: 50; transform: translateY(-110%);
                transition: transform .45s cubic-bezier(.2,.7,.2,1); }
        :host([visible]) { transform: none; }
        nav { display: flex; align-items: center; gap: 24px; padding: 12px clamp(16px, 4vw, 56px);
              background: rgba(255,255,255,.82); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
              border-bottom: 1px dotted var(--line); }
        .me { font-weight: 900; letter-spacing: .02em; color: var(--ink); text-decoration: none; white-space: nowrap; }
        ul { display: flex; gap: 4px; margin: 0 0 0 auto; padding: 0; list-style: none; overflow-x: auto; scrollbar-width: none; }
        a.s { display: block; padding: 6px 10px; border-radius: 99px; color: var(--ink-soft); text-decoration: none;
              font-size: .82rem; white-space: nowrap; transition: background .25s, color .25s; }
        a.s b { font-weight: 800; margin-right: .35em; }
        a.s:hover { color: var(--ink); }
        a.s.on { background: var(--ink); color: #fff; }
        .t { display: inline; }
        @media (max-width: 1100px) { .t { display: none; } a.s b { margin: 0; } }
        .short { display: none; }
        @media (max-width: 760px) { nav { gap: 12px; padding: 10px 12px; } .full { display: none; } .short { display: inline; }
          ul { gap: 2px; } a.s { padding: 7px 10px; font-size: .85rem; } }
      </style>
      <nav>
        <a class="me" href="#"><span class="full">SELENA PASCUAL</span><span class="short">SP</span></a>
        <ul>
          ${s.map((x) => `<li><a class="s" href="#s${x.num}" data-id="s${x.num}"><b>${x.num}</b><span class="t">${x.title}</span></a></li>`).join('')}
          <li><a class="s" href="#contacto" data-id="contacto"><b>✉</b><span class="t">Contacto</span></a></li>
        </ul>
      </nav>`;

    const links = [...this.shadowRoot.querySelectorAll('a.s')];
    const io = new IntersectionObserver((entries) => {
      for (const e of entries) {
        if (!e.isIntersecting) continue;
        links.forEach((l) => l.classList.toggle('on', l.dataset.id === e.target.id));
      }
    }, { rootMargin: '-45% 0px -50% 0px' });
    // Las secciones se crean a la vez que la barra: esperar a que estén en el DOM.
    requestAnimationFrame(() => links.forEach((l) => {
      const target = document.getElementById(l.dataset.id);
      if (target) io.observe(target);
    }));
  }
}

customElements.define('sp-nav', SpNav);
