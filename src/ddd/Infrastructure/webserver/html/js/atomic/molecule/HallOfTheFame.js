// <hall-of-the-fame src="/cv-data/gente.json"></hall-of-the-fame>
// La gente que me ha hecho mejor, como un río de caras que fluye de izquierda a derecha.
// Cada persona: { nombre, frase, linkedin, foto, voto }.
//   voto (1-10, por defecto 5): prioridad interna. No se muestra; cuanto más alto, más a menudo sale
//   en el río y algo más grande es su cara.
// Sin foto, se pintan sus iniciales. Al pasar el ratón (o enfocar) el río se para y abajo sale quién es.
const COLORES = ['#4f97d7', '#bc6ec5', '#2d9574', '#d0a03a', '#ce537a', '#3cb3a8', '#7f8fd0'];
const ALTO_RIO = 240;          // px
const CARRILES = 3;
const CADA_MS = 1700;          // una cara nueva cada tanto
const VELOCIDAD = [38, 58];    // px/s, rango

const iniciales = (nombre) => nombre.split(/\s+/).filter(Boolean).slice(0, 2).map(p => p[0].toUpperCase()).join('');
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const voto = (p) => Math.min(10, Math.max(1, Number(p.voto) || 5));
const azar = (a, b) => a + Math.random() * (b - a);

class HallOfTheFame extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.caras = [];
        this.parado = false;
        this.visible = true;
        this.ultima = -1;
        this.carrilLibre = 0;
    }

    async connectedCallback() {
        const src = this.getAttribute('src') || '/cv-data/gente.json';
        try {
            this.gente = (await fetch(src).then(r => r.json())).map((p, i) => ({ ...p, color: COLORES[i % COLORES.length] }));
        } catch (e) {
            return; // sin datos no se pinta nada
        }
        if (!this.gente.length) return;
        this.render();
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return this.estatico();
        this.arrancar();
    }

    disconnectedCallback() {
        cancelAnimationFrame(this.raf);
        this.io?.disconnect();
    }

    // Elección ponderada por voto, sin repetir la misma persona dos veces seguidas.
    elegir() {
        const pool = this.gente.map((p, i) => ({ p, i })).filter(x => this.gente.length < 2 || x.i !== this.ultima);
        let r = Math.random() * pool.reduce((s, x) => s + voto(x.p), 0);
        for (const x of pool) {
            r -= voto(x.p);
            if (r <= 0) { this.ultima = x.i; return x.p; }
        }
        this.ultima = pool[0].i;
        return pool[0].p;
    }

    nodo(p) {
        const tag = p.linkedin ? 'a' : 'div';
        const el = document.createElement(tag);
        el.className = 'cara';
        el.tabIndex = 0;
        if (p.linkedin) { el.href = p.linkedin; el.target = '_blank'; el.rel = 'noopener'; }
        el.setAttribute('aria-label', `${p.nombre}: ${p.frase}`);
        const t = 56 + voto(p) * 3;
        el.style.cssText = `--c:${p.color};--t:${t}px`;
        el.innerHTML = p.foto
            ? `<img src="${esc(p.foto)}" alt="" loading="lazy">`
            : `<span class="ini">${esc(iniciales(p.nombre))}</span>`;
        el.addEventListener('mouseenter', () => this.mostrar(p));
        el.addEventListener('focus', () => this.mostrar(p));
        return { el, t };
    }

    soltar(x) {
        const p = this.elegir();
        const { el, t } = this.nodo(p);
        const carril = this.carrilLibre;
        this.carrilLibre = (this.carrilLibre + 1 + Math.floor(Math.random() * (CARRILES - 1))) % CARRILES;
        const hueco = (ALTO_RIO - 100) / (CARRILES - 1);
        this.rio.appendChild(el);
        this.caras.push({
            el, t,
            x: x ?? -t - 10,
            base: 20 + carril * hueco,
            v: azar(...VELOCIDAD),
            amp: azar(8, 18),
            fase: azar(0, Math.PI * 2),
            onda: azar(0.006, 0.012),
        });
    }

    arrancar() {
        const ancho = this.rio.clientWidth || 600;
        // Para que no arranque vacío: unas cuantas ya repartidas por el río.
        const iniciales = Math.max(3, Math.round(ancho / 160));
        for (let k = 0; k < iniciales; k++) this.soltar((k + Math.random() * 0.6) * (ancho / iniciales));

        this.io = new IntersectionObserver(([e]) => { this.visible = e.isIntersecting; });
        this.io.observe(this);

        let antes = performance.now(), acumulado = 0;
        const paso = (ahora) => {
            const dt = Math.min(0.05, (ahora - antes) / 1000);
            antes = ahora;
            if (!this.parado && this.visible) {
                acumulado += dt * 1000;
                if (acumulado >= CADA_MS) { acumulado = 0; this.soltar(); }
                const fin = this.rio.clientWidth + 20;
                this.caras = this.caras.filter(c => {
                    c.x += c.v * dt;
                    if (c.x > fin) { c.el.remove(); return false; }
                    const y = c.base + Math.sin(c.fase + c.x * c.onda) * c.amp;
                    c.el.style.transform = `translate(${c.x}px, ${y}px)`;
                    return true;
                });
            }
            this.raf = requestAnimationFrame(paso);
        };
        this.raf = requestAnimationFrame(paso);
    }

    // Sin movimiento: todas en fila, una vez cada una.
    estatico() {
        this.rio.classList.add('quieto');
        this.gente.forEach(p => this.rio.appendChild(this.nodo(p).el));
    }

    mostrar(p) {
        this.pie.innerHTML = `<strong style="color:${p.color}">${esc(p.nombre)}</strong> ${esc(p.frase)}`;
        this.pie.classList.add('on');
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    margin: 0 auto;
                    padding: 15px 15px 20px;
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
                    margin: 0 0 10px;
                    text-align: center;
                    font-size: 1.3em;
                    text-transform: uppercase;
                    letter-spacing: 1.5px;
                }
                .rio {
                    position: relative;
                    height: ${ALTO_RIO}px;
                    overflow: hidden;
                    -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
                            mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
                }
                .rio.quieto {
                    height: auto;
                    display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;
                    padding: 10px 0;
                    -webkit-mask-image: none; mask-image: none;
                }
                .cara {
                    position: absolute; top: 0; left: 0;
                    width: var(--t); height: var(--t);
                    border-radius: 50%;
                    display: grid; place-items: center;
                    background: var(--c);
                    border: 3px solid var(--head2-bg);
                    box-shadow: 0 0 0 2px var(--c), 0 8px 18px rgba(0, 0, 0, .25);
                    overflow: hidden;
                    text-decoration: none;
                    outline: none;
                    will-change: transform;
                    cursor: pointer;
                }
                .quieto .cara { position: relative; }
                .cara img { width: 100%; height: 100%; object-fit: cover; }
                .ini { color: #fff; font-weight: 700; font-size: calc(var(--t) * .3); letter-spacing: 1px; transition: transform .25s; }
                .cara:hover .ini, .cara:focus .ini, .cara:hover img, .cara:focus img { transform: scale(1.15); }
                .cara:focus-visible { box-shadow: 0 0 0 3px var(--green), 0 8px 18px rgba(0, 0, 0, .25); }
                .pie {
                    min-height: 2.6em;
                    margin-top: 8px;
                    text-align: center;
                    font-size: .95em;
                    opacity: .55;
                    transition: opacity .2s;
                }
                .pie.on { opacity: 1; }
                .pie strong { margin-right: 4px; }
            </style>
            <h2>Hall of the fame</h2>
            <div class="rio" part="rio"></div>
            <div class="pie" aria-live="polite">Pasa el ratón por una cara</div>
        `;
        this.rio = this.shadowRoot.querySelector('.rio');
        this.pie = this.shadowRoot.querySelector('.pie');
        const parar = () => { this.parado = true; };
        const seguir = () => { this.parado = false; };
        this.rio.addEventListener('mouseenter', parar);
        this.rio.addEventListener('mouseleave', seguir);
        this.rio.addEventListener('focusin', parar);
        this.rio.addEventListener('focusout', seguir);
        this.rio.addEventListener('touchstart', parar, { passive: true });
    }
}

customElements.define('hall-of-the-fame', HallOfTheFame);
export default HallOfTheFame;
