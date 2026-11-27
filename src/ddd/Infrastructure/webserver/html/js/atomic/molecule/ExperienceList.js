import { baseSheet, sheet, template, stamp, esc, fmtMonth } from '../lib/dom.js';
import '../atom/TagChips.js';
import '../atom/TerminalCast.js';

// <experience-list> — experiencia como acordeón nativo (<details>), sin una línea de JS
// para abrir/cerrar. list.items = timeline.json (sin los proyectos propios).
const own = sheet(`
    :host { display: block; }
    .job { position: relative; padding-left: 28px; }
    .job::before {
        content: ''; position: absolute; left: 7px; top: 26px; bottom: -14px;
        width: 2px; background: var(--pm-border);
    }
    .job:last-child::before { display: none; }
    .dot {
        position: absolute; left: 0; top: 20px; width: 16px; height: 16px; border-radius: 50%;
        background: var(--pm-bg); border: 3px solid var(--pm-accent);
    }
    details { margin-bottom: 14px; }
    summary {
        list-style: none; cursor: pointer;
        display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 4px 16px;
        padding: 16px 20px; border-radius: var(--pm-radius);
        background: var(--pm-surface); border: 1px solid var(--pm-border);
        transition: border-color .2s ease;
    }
    summary::-webkit-details-marker { display: none; }
    summary:hover, details[open] summary { border-color: var(--pm-accent); }
    .company { font-size: 18px; font-weight: 700; color: var(--pm-strong); }
    .position { flex-basis: 100%; font-size: 14px; color: var(--pm-accent); }
    .period { font: 500 13px var(--pm-mono); color: var(--pm-muted); }
    .period::after { content: ' ▾'; color: var(--pm-accent); }
    details[open] .period::after { content: ' ▴'; }
    .projects { display: grid; gap: 14px; padding: 14px 0 4px; }
    .project h4 { font-size: 16px; }
    .project .role { font-size: 13px; margin: 2px 0 10px; }
    .project ul.hl li { position: relative; padding-left: 18px; margin-bottom: 6px; font-size: 15px; }
    .project ul.hl li::before { content: '▸'; position: absolute; left: 0; color: var(--pm-accent-2); }
    .project .wins { margin: 8px 0; font-size: 14px; color: var(--pm-strong); }
    .project tag-chips { margin-top: 10px; }
    .project .links { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .project .links a { font-size: 13px; padding: 6px 12px; }
    terminal-cast { margin-top: 12px; }
`);

const jobTpl = template(`
    <div class="job">
        <span class="dot"></span>
        <details>
            <summary>
                <span class="company"></span>
                <span class="period"></span>
                <span class="position"></span>
            </summary>
            <div class="projects"></div>
        </details>
    </div>
`);

const projectTpl = template(`
    <article class="project card">
        <h4></h4>
        <p class="role muted"></p>
        <ul class="hl"></ul>
        <p class="wins"></p>
        <tag-chips></tag-chips>
        <nav class="links"></nav>
    </article>
`);

function renderProject(p) {
    const frag = stamp(projectTpl);
    frag.querySelector('h4').textContent = p.name;
    frag.querySelector('.role').textContent = p.role || '';
    frag.querySelector('.hl').innerHTML = (p.highlights || []).map(h => `<li>${esc(h)}</li>`).join('');
    const wins = frag.querySelector('.wins');
    (p.achievements || []).length
        ? wins.textContent = `🏆 ${p.achievements.join(' · ')}`
        : wins.remove();
    frag.querySelector('tag-chips').tags = p.technologies || [];
    const links = frag.querySelector('.links');
    links.innerHTML = (p.links || [])
        .map(l => `<a class="btn" href="${esc(l.url)}" target="_blank" rel="noopener">${esc(l.label)} ↗</a>`).join('');
    if (p.demo?.cast) {
        const cast = document.createElement('terminal-cast');
        cast.setAttribute('src', p.demo.cast);
        cast.setAttribute('titulo', p.demo.titulo || '');
        links.after(cast);
    }
    return frag;
}

class ExperienceList extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' }).adoptedStyleSheets = [baseSheet, own];
    }

    set items(jobs) {
        const tray = document.createDocumentFragment();     // la bandeja grande
        jobs.forEach((job, i) => {
            const frag = stamp(jobTpl);
            frag.querySelector('.company').textContent = job.company;
            frag.querySelector('.position').textContent = job.position;
            frag.querySelector('.period').textContent = `${fmtMonth(job.startDate)} — ${fmtMonth(job.endDate)}`;
            if (i === 0) frag.querySelector('details').open = true;
            frag.querySelector('.projects').append(...job.projects.map(renderProject));
            tray.append(frag);
        });
        this.shadowRoot.replaceChildren(tray);              // un solo repintado
    }
}

customElements.define('experience-list', ExperienceList);
export default ExperienceList;
