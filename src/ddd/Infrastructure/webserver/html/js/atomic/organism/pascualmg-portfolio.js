import { baseSheet, sheet, template, stamp, esc } from '../lib/dom.js';
import '../molecule/PortfolioHero.js';
import '../molecule/ProjectCard.js';
import '../molecule/ExperienceList.js';
import '../atom/TagChips.js';
import '../molecule/HallOfTheFame.js';
import '../atom/YastModal.js';
import '../molecule/AboutThisPortfolio.js';

// <pascualmg-portfolio> — el organismo. Una sola fuente de verdad: los mismos JSON que el CV
// (/cv-data/*.json). Si cambias el CV, cambia el portfolio.
//
// TOKENS: todo el diseño cuelga de las variables --pm-* de aqui abajo. Heredan a traves del
// shadow DOM de cada hijo, asi que el diseño de Selena se aplica tocando SOLO este bloque.
const own = sheet(`
    :host {
        --pm-bg: var(--bg1, #292b2e);
        --pm-surface: var(--bg2, #212026);
        --pm-text: var(--base, #b2b2b2);
        --pm-strong: var(--cursor, #e3dedd);
        --pm-muted: var(--base-dim, #686868);
        --pm-border: color-mix(in srgb, var(--border, #5d4d7a) 70%, transparent);
        --pm-accent: var(--head1, #4f97d7);
        --pm-accent-2: var(--head2, #2d9574);
        --pm-pop: var(--comp, #c56ec3);
        --pm-on-accent: var(--bg1, #292b2e);
        --pm-term: var(--bg3, #100a14);
        --pm-term-text: var(--suc, #86dc2f);
        --pm-radius: 16px;
        --pm-font: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        --pm-mono: 'JetBrains Mono', 'Fira Code', ui-monospace, monospace;

        display: block;
        max-width: 1040px;
        margin: 0 auto;
        padding: 8px 4px 40px;
    }
    section { margin-top: clamp(48px, 8vw, 88px); }
    .head { margin-bottom: 22px; }
    .head h2 { font-size: clamp(24px, 3.4vw, 32px); margin-top: 6px; letter-spacing: -.01em; }
    .head p.sub { margin-top: 8px; max-width: 62ch; }
    .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); }
    .focus .card { display: flex; flex-direction: column; gap: 10px; }
    .focus .icon { font-size: 28px; }
    .focus h3 { font-size: 19px; }
    .focus .card.star {
        border-color: var(--pm-accent);
        background:
            radial-gradient(120% 90% at 100% 0%, color-mix(in srgb, var(--pm-accent) 20%, transparent), transparent 60%),
            var(--pm-surface);
    }
    .focus a.more { margin-top: auto; font-weight: 600; }
    .principles li { display: flex; gap: 12px; align-items: flex-start; }
    .principles li + li { margin-top: 14px; }
    .principles b { color: var(--pm-strong); }
    .principles .n { font: 700 13px var(--pm-mono); color: var(--pm-accent-2); padding-top: 3px; }
    .two { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
    .facts { display: grid; gap: 10px; margin-top: 18px; font-size: 15px; }
    .facts b { color: var(--pm-strong); }
    .about { display: flex; justify-content: center; margin-top: 48px; }
    .status { text-align: center; padding: 80px 0; }

    .reveal { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
    .reveal.in { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1; transform: none; transition: none; } }
`);

const tpl = template(`
    <portfolio-hero></portfolio-hero>

    <section class="focus reveal">
        <div class="head">
            <p class="eyebrow">01 · Lo que hago</p>
            <h2>Del legacy que nadie toca a un comando que todo el mundo usa</h2>
        </div>
        <div class="grid">
            <article class="card star">
                <span class="icon">🚀</span>
                <h3>autonix: el entorno, primero</h3>
                <p>Emulo el ecosistema legacy entero en local, declarado con Nix y orquestado
                   con process-compose. Local por defecto, producción imposible por diseño.</p>
                <a class="more" href="/blog/pascual/autonix-un-comando-todo-el-legacy-cero-demonios">Leer la historia →</a>
            </article>
            <article class="card">
                <span class="icon">🤖</span>
                <h3>Proyectos IA-ready</h3>
                <p>AGENTS.md, documentación agnóstica y un agente experto residente: el
                   conocimiento deja de morir con quien se va, y una IA puede operar el código.</p>
                <a class="more" href="/blog/ambrosio/el-entorno-de-desarrollo-es-codigo">El entorno es código →</a>
            </article>
            <article class="card">
                <span class="icon">🛠️</span>
                <h3>Mis propias herramientas</h3>
                <p>Un framework PHP asíncrono en producción y una librería con recipe oficial
                   de Symfony Flex. Construyo lo que me falta y lo publico.</p>
                <a class="more" href="https://github.com/pascualmg" target="_blank" rel="noopener">GitHub →</a>
            </article>
        </div>
    </section>

    <section class="reveal">
        <div class="head">
            <p class="eyebrow">02 · Open source</p>
            <h2>Proyectos propios</h2>
            <p class="sub muted">Publicados, en producción y con gente usándolos.</p>
        </div>
        <div class="grid projects"></div>
    </section>

    <section class="reveal">
        <div class="head">
            <p class="eyebrow">03 · Trayectoria</p>
            <h2>Experiencia</h2>
            <p class="sub muted">Pulsa cada etapa para ver los proyectos.</p>
        </div>
        <experience-list></experience-list>
    </section>

    <section class="reveal">
        <div class="head">
            <p class="eyebrow">04 · Criterio</p>
            <h2>Cómo trabajo</h2>
        </div>
        <div class="two">
            <article class="card">
                <ul class="principles">
                    <li><span class="n">01</span><p><b>Tests primero.</b> ATDD: el comportamiento acordado con negocio se escribe antes que el código.</p></li>
                    <li><span class="n">02</span><p><b>El mismo lenguaje.</b> DDD de punta a punta: lo que se habla con negocio es lo que acaba en el código.</p></li>
                    <li><span class="n">03</span><p><b>Emular antes de reformar.</b> Primero el arnés, luego los cambios. Nada a mano.</p></li>
                    <li><span class="n">04</span><p><b>IA con criterio.</b> Para hacer las cosas mejor, no solo más rápido. Lo que es humo, fuera.</p></li>
                </ul>
            </article>
            <article class="card">
                <h3>Stack</h3>
                <tag-chips class="stack" style="display:block; margin-top:12px"></tag-chips>
                <div class="facts"></div>
            </article>
        </div>
    </section>

    <section class="reveal">
        <hall-of-the-fame src="/cv-data/gente.json"></hall-of-the-fame>
        <div class="about">
            <yast-modal button-text="Qué tiene de especial este portfolio">
                <about-this-portfolio slot="content"></about-this-portfolio>
            </yast-modal>
        </div>
    </section>
`);

const getJson = (path) => fetch(path).then(r => {
    if (!r.ok) throw new Error(`${path}: ${r.status}`);
    return r.json();
});

class PortfolioElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' }).adoptedStyleSheets = [baseSheet, own];
    }

    async connectedCallback() {
        this.shadowRoot.innerHTML = `<p class="status muted">Cargando…</p>`;
        try {
            const [contact, timeline, skills] = await Promise.all([
                getJson('/cv-data/contact.json'),
                getJson('/cv-data/timeline.json'),
                getJson('/cv-data/skills.json'),
            ]);
            this.render(contact, timeline, skills);
        } catch (e) {
            console.error(e);
            this.shadowRoot.innerHTML = `<p class="status muted">No he podido cargar el portfolio. <a href="/cv">Mira el CV</a>.</p>`;
        }
    }

    render(contact, timeline, skills) {
        const frag = stamp(tpl);

        frag.querySelector('portfolio-hero').data = contact;

        const own = timeline.filter(t => t.tipo === 'propio').flatMap(t => t.projects);
        frag.querySelector('.projects').append(...own.map(p => {
            const card = document.createElement('project-card');
            card.project = p;
            return card;
        }));

        frag.querySelector('experience-list').items = timeline.filter(t => t.tipo !== 'propio');

        frag.querySelector('.stack').tags = skills.technologies || [];
        frag.querySelector('.facts').innerHTML = [
            ...(skills.education || []).map(e => `<p>🎓 <b>${esc(e.title)}</b> <span class="muted">${esc(e.period)}</span></p>`),
            `<p>🗣️ ${(skills.languages || []).map(l => `<b>${esc(l.name)}</b> ${esc(l.level)}`).join(' · ')}</p>`,
        ].join('');

        this.shadowRoot.replaceChildren(frag);
        this.observeReveal();
    }

    observeReveal() {
        const io = new IntersectionObserver(entries => entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
        }), { threshold: 0.08 });
        this.shadowRoot.querySelectorAll('.reveal').forEach(el => io.observe(el));
    }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement;
