// <hall-of-the-fame src="/cv-data/gente.json"></hall-of-the-fame>
// La gente que me ha hecho mejor, como un río de caras.
//
// Datos (src): [{ nombre, frase, linkedin, foto, voto }]
//   voto (1-10, por defecto 5): prioridad interna, no se muestra. Más voto = sale más a menudo
//   (con 10 sale el doble que con 5) y su cara es algo más grande.
//   Sin foto se pintan sus iniciales.
//
// Atributos (todos opcionales; se pueden cambiar en vivo):
//   src        URL del JSON                         (/cv-data/gente.json)
//   direction  rtl | ltr: hacia dónde corre el río  (rtl)
//   speed      velocidad media del flujo, px/s      (48)
//   size       diámetro base de las bolas, px       (56)  el voto suma hasta +30 px
//   width      ancho, cualquier valor CSS           (100%)
//   height     alto del río, px                     (240)
//   lanes      carriles                             (3)
//   interval   ms entre cara y cara                 (1700)
//   bob        amplitud de la flotación, px         (14)  0 = sin flotar
//   heading    título; heading="" lo quita          (Hall of the fame)
//
// Ratón encima (o foco, o toque): el río deja de avanzar, pero las caras siguen flotando,
// y abajo sale quién es.
const COLORES = ['#4f97d7', '#bc6ec5', '#2d9574', '#d0a03a', '#ce537a', '#3cb3a8', '#7f8fd0'];
const DEF = { direction: 'rtl', speed: 48, size: 56, width: '100%', height: 240, lanes: 3, interval: 1700, bob: 14, heading: 'Hall of the fame' };

const iniciales = (nombre) => nombre.split(/\s+/).filter(Boolean).slice(0, 2).map(p => p[0].toUpperCase()).join('');
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const voto = (p) => Math.min(10, Math.max(1, Number(p.voto) || 5));
const azar = (a, b) => a + Math.random() * (b - a);
const num = (v, d, min = 0) => { if (v === null || v === '') return d; const n = Number(v); return Number.isFinite(n) && n >= min ? n : d; };

class HallOfTheFame extends HTMLElement {
    static get observedAttributes() { return ['direction', 'speed', 'size', 'width', 'height', 'lanes', 'interval', 'bob', 'heading']; }

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.caras = [];
        this.parado = false;
        this.visible = true;
        this.ultima = -1;
        this.siguienteCarril = 0;
        this.leer();
    }

    leer() {
        const a = (n) => this.getAttribute(n);
        this.cfg = {
            direction: a('direction') === 'ltr' ? 'ltr' : 'rtl',
            speed: num(a('speed'), DEF.speed),
            size: num(a('size'), DEF.size, 10),
            width: a('width') || DEF.width,
            height: num(a('height'), DEF.height, 60),
            lanes: Math.round(num(a('lanes'), DEF.lanes, 1)),
            interval: num(a('interval'), DEF.interval, 100),
            bob: num(a('bob'), DEF.bob),
            heading: this.hasAttribute('heading') ? a('heading') : DEF.heading,
        };
    }

    attributeChangedCallback() {
        this.leer();
        if (!this.rio) return;
        this.style.width = this.cfg.width;
        this.rio.style.height = this.cfg.height + 'px';
        this.titulo.textContent = this.cfg.heading;
        this.titulo.hidden = !this.cfg.heading;
    }

    async connectedCallback() {
        if (this.rio) return; // ya montado (se ha movido en el DOM)
        const src = this.getAttribute('src') || '/cv-data/gente.json';
        try {
            this.gente = (await fetch(src).then(r => r.json())).map((p, i) => ({ ...p, color: COLORES[i % COLORES.length] }));
        } catch (e) {
            return; // sin datos no se pinta nada
        }
        if (!this.gente.length) return;
        this.render();
        this.attributeChangedCallback();
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
        const el = document.createElement(p.linkedin ? 'a' : 'div');
        el.className = 'cara';
        el.tabIndex = 0;
        if (p.linkedin) { el.href = p.linkedin; el.target = '_blank'; el.rel = 'noopener'; }
        el.setAttribute('aria-label', `${p.nombre}${p.frase ? ': ' + p.frase : ''}`);
        const t = this.cfg.size + voto(p) * 3;
        el.style.cssText = `--c:${p.color};--t:${t}px`;
        el.innerHTML = p.foto
            ? `<img src="${esc(p.foto)}" alt="" loading="lazy">`
            : `<span class="ini">${esc(iniciales(p.nombre))}</span>`;
        el.addEventListener('mouseenter', () => this.mostrar(p));
        el.addEventListener('focus', () => this.mostrar(p));
        return { el, t };
    }

    // d = distancia recorrida en el sentido del flujo; así el sentido se puede cambiar en vivo.
    soltar(d) {
        const p = this.elegir();
        const { el, t } = this.nodo(p);
        const carriles = this.cfg.lanes;
        const carril = this.siguienteCarril % carriles;
        this.siguienteCarril = carriles > 1 ? (carril + 1 + Math.floor(Math.random() * (carriles - 1))) % carriles : 0;
        this.rio.appendChild(el);
        this.caras.push({
            el, t, carril,
            d: d ?? -t - 10,
            v: azar(0.8, 1.2),                 // multiplicador sobre speed
            amp: azar(0.6, 1.3),               // multiplicador sobre bob
            fase: azar(0, Math.PI * 2),
            ritmo: azar(0.7, 1.3),             // rad/s de la flotación
        });
    }

    arrancar() {
        const ancho = this.rio.clientWidth || 600;
        // Para que no arranque vacío: unas cuantas ya repartidas por el río.
        const n = Math.max(3, Math.round(ancho / 160));
        for (let k = 0; k < n; k++) this.soltar((k + Math.random() * 0.6) * (ancho / n));

        this.io = new IntersectionObserver(([e]) => { this.visible = e.isIntersecting; });
        this.io.observe(this);

        let antes = performance.now(), acumulado = 0, reloj = 0;
        const paso = (ahora) => {
            const dt = Math.min(0.05, (ahora - antes) / 1000);
            antes = ahora;
            if (this.visible) {
                const { speed, bob, direction, lanes, height, interval } = this.cfg;
                const W = this.rio.clientWidth;
                reloj += dt;
                if (!this.parado) {
                    acumulado += dt * 1000;
                    if (acumulado >= interval) { acumulado = 0; this.soltar(); }
                }
                this.caras = this.caras.filter(c => {
                    if (!this.parado) c.d += speed * c.v * dt;
                    if (c.d > W + 20) { c.el.remove(); return false; }
                    const x = direction === 'rtl' ? W - c.d - c.t : c.d;
                    // carriles repartidos en el alto disponible, dejando sitio a la bola y a la flotación
                    const libre = Math.max(0, height - c.t - 2 * bob);
                    const base = bob + (lanes > 1 ? (c.carril % lanes) * libre / (lanes - 1) : libre / 2);
                    const y = base + Math.sin(c.fase + reloj * c.ritmo) * bob * c.amp;
                    c.el.style.transform = `translate(${x}px, ${y}px)`;
                    return true;
                });
            }
            this.raf = requestAnimationFrame(paso);
        };
        this.raf = requestAnimationFrame(paso);
    }

    // Sin movimiento (prefers-reduced-motion): todas en fila, una vez cada una.
    estatico() {
        this.rio.classList.add('quieto');
        this.gente.forEach(p => this.rio.appendChild(this.nodo(p).el));
    }

    mostrar(p) {
        this.pie.innerHTML = `<strong style="color:${p.color}">${esc(p.nombre)}</strong>${p.frase ? ' ' + esc(p.frase) : ''}`;
        this.pie.classList.add('on');
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    box-sizing: border-box;
                    max-width: 100%;
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
                h2[hidden] { display: none; }
                .rio {
                    position: relative;
                    overflow: hidden;
                    -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
                            mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
                }
                .rio.quieto {
                    height: auto !important;
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
                .ini { color: #fff; font-weight: 700; font-size: calc(var(--t) * .3); letter-spacing: 1px; }
                .cara img, .ini { transition: transform .25s; }
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
            <h2 part="heading"></h2>
            <div class="rio" part="river"></div>
            <div class="pie" part="caption" aria-live="polite">Pasa el ratón por una cara</div>
        `;
        this.titulo = this.shadowRoot.querySelector('h2');
        this.rio = this.shadowRoot.querySelector('.rio');
        this.pie = this.shadowRoot.querySelector('.pie');
        const parar = () => { this.parado = true; };
        const seguir = () => { this.parado = false; };
        this.rio.addEventListener('mouseenter', parar);
        this.rio.addEventListener('mouseleave', seguir);
        this.rio.addEventListener('focusin', parar);
        this.rio.addEventListener('focusout', seguir);
        this.rio.addEventListener('touchstart', parar, { passive: true });
        this.rio.addEventListener('touchend', () => setTimeout(seguir, 2500), { passive: true });
    }
}

customElements.define('hall-of-the-fame', HallOfTheFame);
export default HallOfTheFame;
