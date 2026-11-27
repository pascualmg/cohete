import { baseSheet, sheet, template, stamp, esc } from '../lib/dom.js';
import './SocialLinks.js';

// <portfolio-hero> — la portada. Recibe contact.json por propiedad: hero.data = {...}
const own = sheet(`
    :host { display: block; }
    .hero {
        position: relative; overflow: hidden;
        padding: clamp(28px, 6vw, 64px) clamp(20px, 5vw, 56px);
        border-radius: calc(var(--pm-radius) * 1.5);
        border: 1px solid var(--pm-border);
        background:
            radial-gradient(120% 80% at 0% 0%, color-mix(in srgb, var(--pm-accent) 22%, transparent), transparent 60%),
            radial-gradient(90% 70% at 100% 100%, color-mix(in srgb, var(--pm-pop) 18%, transparent), transparent 60%),
            var(--pm-surface);
    }
    h1 {
        margin: 12px 0 8px;
        font-size: clamp(34px, 6.5vw, 62px);
        line-height: 1.05; letter-spacing: -.02em;
        color: var(--pm-strong);
    }
    .title { font-size: clamp(16px, 2.2vw, 20px); color: var(--pm-accent); font-weight: 600; }
    .lead { margin-top: 18px; max-width: 60ch; font-size: clamp(16px, 2vw, 19px); }
    .lead strong { color: var(--pm-strong); }
    .prompt {
        display: inline-block; margin-top: 22px;
        font: 500 14px/1 var(--pm-mono);
        padding: 10px 14px; border-radius: 10px;
        background: var(--pm-term); color: var(--pm-term-text);
        border: 1px solid var(--pm-border);
    }
    .prompt::before { content: '$ '; color: var(--pm-accent-2); }
    .ctas { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 26px; }
    .meta { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-top: 28px; }
    .loc { font-size: 14px; }
`);

const tpl = template(`
    <header class="hero">
        <p class="eyebrow">Team Lead · Backend · IA aplicada</p>
        <h1></h1>
        <p class="title"></p>
        <p class="lead">
            Convierto sistemas <strong>legacy</strong> en entornos que se levantan con
            <strong>un comando</strong> y que una <strong>IA puede operar sin miedo</strong>.
            Más de 13 años de PHP/Symfony, DDD y criterio de la vieja escuela.
        </p>
        <code class="prompt">nix run .#dev -- full</code>
        <nav class="ctas">
            <a class="btn primary" href="/cv" target="_blank" rel="noopener">Descargar CV</a>
            <a class="btn" href="/blog/pascual/autonix-un-comando-todo-el-legacy-cero-demonios">Qué es autonix</a>
            <a class="btn" href="/blog">Leer el blog</a>
        </nav>
        <div class="meta">
            <span class="loc muted"></span>
            <social-links></social-links>
        </div>
    </header>
`);

class PortfolioHero extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' }).adoptedStyleSheets = [baseSheet, own];
    }

    set data(c) {
        const frag = stamp(tpl);                       // clonamos el molde en la bandeja
        frag.querySelector('h1').textContent = c.name;
        frag.querySelector('.title').textContent = c.title;
        frag.querySelector('.loc').textContent = `📍 ${c.location}`;
        const byName = Object.fromEntries((c.links || []).map(l => [l.name.toLowerCase(), l.url]));
        const links = [
            { name: 'email', url: `mailto:${c.email}` },
            { name: 'linkedin', url: byName.linkedin },
            { name: 'github', url: byName.github },
            { name: 'twitter', url: byName.twitter },
        ].filter(l => l.url);
        frag.querySelector('social-links').setAttribute('links', JSON.stringify(links));
        this.shadowRoot.replaceChildren(frag);          // y la metemos de golpe
    }
}

customElements.define('portfolio-hero', PortfolioHero);
export default PortfolioHero;
