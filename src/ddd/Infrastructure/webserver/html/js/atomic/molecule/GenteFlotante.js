// <gente-flotante src="/cv-data/gente.json"></gente-flotante>
// Las personas que me han hecho mejor: caras que flotan; al pasar (o tocar) una, su nombre y su frase.
// Cada persona: { nombre, frase, linkedin, foto }. Sin foto, se pintan sus iniciales.
const COLORES = ['#4f97d7', '#bc6ec5', '#2d9574', '#d0a03a', '#ce537a', '#3cb3a8', '#7f8fd0'];

const iniciales = (nombre) => nombre.split(/\s+/).filter(Boolean).slice(0, 2).map(p => p[0].toUpperCase()).join('');
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

class GenteFlotante extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    async connectedCallback() {
        const src = this.getAttribute('src') || '/cv-data/gente.json';
        let gente = [];
        try {
            gente = await fetch(src).then(r => r.json());
        } catch (e) {
            return; // sin datos no se pinta nada
        }
        this.render(gente);
    }

    cara(p, i) {
        const color = COLORES[i % COLORES.length];
        const dentro = p.foto
            ? `<img src="${esc(p.foto)}" alt="" loading="lazy">`
            : `<span class="ini">${esc(iniciales(p.nombre))}</span>`;
        const tag = p.linkedin ? 'a' : 'div';
        const href = p.linkedin ? ` href="${esc(p.linkedin)}" target="_blank" rel="noopener"` : '';
        const dx = (i % 2 ? 10 : -8), dy = (i % 3 ? -12 : 9);
        return `
            <${tag} class="cara" tabindex="0"${href} style="--c:${color};--dx:${dx}px;--dy:${dy}px;animation-delay:${-i * 1.4}s" aria-label="${esc(p.nombre)}: ${esc(p.frase)}">
                <span class="bola">${dentro}</span>
                <span class="tip"><strong>${esc(p.nombre)}</strong>${esc(p.frase)}</span>
            </${tag}>`;
    }

    render(gente) {
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    margin: 0 auto;
                    padding: 15px 15px 25px;
                    background: var(--head2-bg);
                    border-radius: 15px;
                    border: 1px solid var(--border);
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                    color: var(--base);
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                h2 {
                    color: var(--head1);
                    border-bottom: 2px solid var(--green);
                    padding-bottom: 12px;
                    margin: 0 0 30px;
                    text-align: center;
                    font-size: 1.3em;
                    text-transform: uppercase;
                    letter-spacing: 1.5px;
                }
                .nube {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    gap: 28px 36px;
                    padding-bottom: 70px;
                }
                .cara {
                    position: relative;
                    display: block;
                    text-decoration: none;
                    color: inherit;
                    animation: flota 7s ease-in-out infinite alternate;
                    outline: none;
                }
                .bola {
                    width: 76px; height: 76px;
                    border-radius: 50%;
                    display: grid; place-items: center;
                    background: var(--c);
                    border: 3px solid var(--head2-bg);
                    box-shadow: 0 0 0 2px var(--c), 0 8px 18px rgba(0, 0, 0, .25);
                    overflow: hidden;
                    transition: transform .25s;
                }
                .bola img { width: 100%; height: 100%; object-fit: cover; }
                .ini { color: #fff; font-weight: 700; font-size: 1.5em; letter-spacing: 1px; }
                .tip {
                    position: absolute;
                    top: 88px; left: 50%;
                    transform: translateX(-50%);
                    width: max-content; max-width: 220px;
                    background: var(--bg, #fff);
                    color: var(--base);
                    border: 1px solid var(--border);
                    border-radius: 10px;
                    padding: 8px 12px;
                    font-size: .9em;
                    text-align: center;
                    box-shadow: 0 6px 16px rgba(0, 0, 0, .2);
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity .2s;
                    z-index: 2;
                }
                .tip strong { display: block; color: var(--head1); margin-bottom: 2px; }
                .cara:hover .bola, .cara:focus .bola { transform: scale(1.12); }
                .cara:hover .tip, .cara:focus .tip { opacity: 1; }
                .cara:focus-visible .bola { box-shadow: 0 0 0 3px var(--green), 0 8px 18px rgba(0, 0, 0, .25); }
                @keyframes flota { from { transform: translate(0, 0); } to { transform: translate(var(--dx), var(--dy)); } }
                @media (prefers-reduced-motion: reduce) { .cara { animation: none; } }
            </style>
            <h2>La gente que me ha hecho mejor</h2>
            <div class="nube">${gente.map((p, i) => this.cara(p, i)).join('')}</div>
        `;
    }
}

customElements.define('gente-flotante', GenteFlotante);
export default GenteFlotante;
