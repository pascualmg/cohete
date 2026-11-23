// <sp-hero>: la portada. El degradado es el fondo original de su PDF, vivo.
class SpHero extends HTMLElement {
  set data(p) { this._p = p; }

  connectedCallback() {
    const p = this._p;
    const [first, ...rest] = p.name.toUpperCase().split(' ');
    this.attachShadow({ mode: 'open' }).innerHTML = `
      <style>
        :host { display: block; position: relative; height: 100svh; min-height: 520px; overflow: hidden; background: var(--sage); }
        .bg { position: absolute; inset: -8%; background: url(/html/selena/img/fondo.webp) center / cover;
              animation: drift 26s ease-in-out infinite alternate; }
        .blob { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .55; mix-blend-mode: soft-light; }
        .b1 { width: 46vmax; height: 46vmax; background: var(--blush); top: -10%; left: 5%; animation: float1 19s ease-in-out infinite alternate; }
        .b2 { width: 38vmax; height: 38vmax; background: var(--cream); bottom: -15%; right: 0; animation: float2 23s ease-in-out infinite alternate; }
        @keyframes drift { from { transform: scale(1) translate(0,0) } to { transform: scale(1.12) translate(-2%, 2%) } }
        @keyframes float1 { to { transform: translate(18vw, 12vh) } }
        @keyframes float2 { to { transform: translate(-20vw, -10vh) } }
        .txt { position: relative; height: 100%; display: flex; flex-direction: column; justify-content: center;
               padding: 0 clamp(24px, 17.5vw, 260px); }
        h1 { margin: 0; font-weight: 900; font-size: clamp(2.6rem, 6.4vw, 5.6rem); letter-spacing: -.01em; line-height: .95; }
        h1 span { display: inline-block; opacity: 0; transform: translateY(.4em); animation: up .9s cubic-bezier(.2,.7,.2,1) forwards; }
        h1 span:nth-child(2) { animation-delay: .12s; }
        .role { margin: .5em 0 0; font-weight: 300; font-size: clamp(1.3rem, 2.6vw, 2.2rem); opacity: 0; animation: up .9s .3s forwards; }
        .areas { margin: 1.6em 0 0; font-size: clamp(.95rem, 1.4vw, 1.15rem); opacity: 0; animation: up .9s .45s forwards; }
        .areas b { font-weight: 400; white-space: nowrap; } .areas i { font-style: normal; margin: 0 .45em; opacity: .7; }
        @keyframes up { to { opacity: 1; transform: none } }
        a.down { position: absolute; left: 50%; bottom: 28px; translate: -50% 0; color: var(--ink); text-decoration: none;
                 font-size: .75rem; letter-spacing: .25em; text-transform: uppercase; display: flex; flex-direction: column;
                 align-items: center; gap: 10px; opacity: 0; animation: up 1s 1s forwards; }
        a.down::after { content: ""; width: 1px; height: 38px; background: var(--ink); animation: line 2.2s ease-in-out infinite; transform-origin: top; }
        @keyframes line { 0% { transform: scaleY(0) } 50% { transform: scaleY(1) } 100% { transform: scaleY(1); opacity: 0 } }
        @media (prefers-reduced-motion: reduce) { .bg, .blob { animation: none } }
      </style>
      <div class="bg"></div><div class="blob b1"></div><div class="blob b2"></div>
      <div class="txt">
        <h1><span>${first}</span> <span>${rest.join(' ')}</span></h1>
        <p class="role">${p.role}</p>
        <p class="areas">${p.heroAreas.map((a) => `<b>${a}</b>`).join('<i>·</i>')}</p>
      </div>
      <a class="down" href="#s01">Ver trabajos</a>`;
  }
}

customElements.define('sp-hero', SpHero);
