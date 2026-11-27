import { baseSheet, sheet, template, stamp, esc } from '../lib/dom.js';
import '../atom/TagChips.js';

// <project-card> — tarjeta de proyecto propio. card.project = {name, role, highlights, achievements, technologies, links}
const own = sheet(`
    :host { display: block; height: 100%; }
    .card { height: 100%; display: flex; flex-direction: column; gap: 14px; transition: transform .2s ease, border-color .2s ease; }
    .card:hover { transform: translateY(-3px); border-color: var(--pm-accent); }
    h3 { font-size: 19px; }
    .role { font-size: 13px; margin-top: 4px; }
    .hl li { position: relative; padding-left: 18px; margin-bottom: 6px; font-size: 15px; }
    .hl li::before { content: '▸'; position: absolute; left: 0; color: var(--pm-accent-2); }
    .wins { display: grid; gap: 6px; }
    .wins li {
        font-size: 14px; padding: 8px 12px; border-radius: 10px;
        background: color-mix(in srgb, var(--pm-accent-2) 12%, transparent);
        border-left: 3px solid var(--pm-accent-2);
        color: var(--pm-strong);
    }
    .links { display: flex; flex-wrap: wrap; gap: 8px; margin-top: auto; }
    .links a { font-size: 13px; padding: 6px 12px; }
`);

const tpl = template(`
    <article class="card">
        <header>
            <h3></h3>
            <p class="role muted"></p>
        </header>
        <ul class="hl"></ul>
        <ul class="wins"></ul>
        <tag-chips></tag-chips>
        <nav class="links"></nav>
    </article>
`);

class ProjectCard extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' }).adoptedStyleSheets = [baseSheet, own];
    }

    set project(p) {
        const frag = stamp(tpl);
        frag.querySelector('h3').textContent = p.name;
        frag.querySelector('.role').textContent = p.role || '';
        frag.querySelector('.hl').innerHTML = (p.highlights || []).slice(0, 3)
            .map(h => `<li>${esc(h)}</li>`).join('');
        const wins = frag.querySelector('.wins');
        const achievements = p.achievements || [];
        achievements.length
            ? wins.innerHTML = achievements.map(a => `<li>🏆 ${esc(a)}</li>`).join('')
            : wins.remove();
        frag.querySelector('tag-chips').tags = p.technologies || [];
        frag.querySelector('.links').innerHTML = (p.links || [])
            .map(l => `<a class="btn" href="${esc(l.url)}" target="_blank" rel="noopener">${esc(l.label)} ↗</a>`).join('');
        this.shadowRoot.replaceChildren(frag);
    }
}

customElements.define('project-card', ProjectCard);
export default ProjectCard;
