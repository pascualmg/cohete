// <sp-section>: un bloque del portfolio (01. Brand Experience, 02. Retail...).
// Cabecera con número, título y etiquetas, como en su PDF, y debajo sus proyectos.
import './sp-project.js';

class SpSection extends HTMLElement {
  set data(s) { this._s = s; }

  connectedCallback() {
    const s = this._s;
    const root = this.attachShadow({ mode: 'open' });
    root.innerHTML = `
      <style>
        :host { display: block; padding: clamp(72px, 10vw, 140px) clamp(16px, 4vw, 56px) 0; max-width: 1480px; margin: 0 auto;
                scroll-margin-top: 40px; }
        header { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 8px 32px;
                 padding-bottom: 14px; border-bottom: 1.5px dotted var(--line); }
        h2 { margin: 0; font-weight: 900; text-transform: uppercase; font-size: clamp(1.7rem, 3.4vw, 2.8rem); letter-spacing: -.005em; }
        .tags { font-size: .85rem; color: var(--ink-soft); }
        .tags i { font-style: normal; margin: 0 .5em; }
        .client { margin: 28px 0 0; font-weight: 300; font-size: clamp(1.15rem, 1.8vw, 1.45rem); }
        .intro p { margin: 16px 0 0; max-width: 68ch; line-height: 1.6; font-size: 1.02rem; }
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .9s, transform .9s cubic-bezier(.2,.7,.2,1); }
        .reveal.in { opacity: 1; transform: none; }
      </style>
      <header class="reveal">
        <h2>${s.num}. ${s.title}</h2>
        <div class="tags">${s.tags.join('<i>·</i>')}</div>
      </header>
      <p class="client reveal">${s.client}</p>
      <div class="intro reveal">${s.intro.map((t) => `<p>${t}</p>`).join('')}</div>
      <div class="projects"></div>`;

    const box = root.querySelector('.projects');
    for (const p of s.projects) {
      const el = document.createElement('sp-project');
      el.data = p;
      box.append(el);
    }

    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    }), { threshold: 0.15 });
    root.querySelectorAll('.reveal').forEach((el) => io.observe(el));
  }
}

customElements.define('sp-section', SpSection);
