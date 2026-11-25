// <terminal-cast src="/casts/x.cast" titulo="..." linkedin="https://...">
// Reproduce una grabación real de terminal (asciinema) dentro de un proyecto del portfolio.
// La grabación se carga solo cuando el componente entra en pantalla.
const PLAYER_VERSION = '3.8.0';
const PLAYER_BASE = `https://cdn.jsdelivr.net/npm/asciinema-player@${PLAYER_VERSION}/dist/bundle`;

let playerPromise = null;
function loadPlayer() {
    if (window.AsciinemaPlayer) return Promise.resolve(window.AsciinemaPlayer);
    if (!playerPromise) {
        playerPromise = new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = `${PLAYER_BASE}/asciinema-player.min.js`;
            s.onload = () => resolve(window.AsciinemaPlayer);
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }
    return playerPromise;
}

class TerminalCast extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        const titulo = this.getAttribute('titulo') || '';
        const linkedin = this.getAttribute('linkedin');
        this.shadowRoot.innerHTML = `
            <link rel="stylesheet" href="${PLAYER_BASE}/asciinema-player.css">
            <style>
                :host { display: block; margin: 12px 0; }
                figure { margin: 0; border-radius: 8px; overflow: hidden; background: #0a100a; }
                .player { min-height: 120px; }
                figcaption { display: flex; flex-wrap: wrap; gap: 8px 16px; justify-content: space-between;
                             font-size: .85em; padding: 8px 2px 0; opacity: .85; }
                a { color: inherit; }
            </style>
            <figure>
                <div class="player"></div>
            </figure>
            <figcaption>
                <span>${titulo}</span>
                ${linkedin ? `<a href="${linkedin}" target="_blank" rel="noopener">Ver el post en LinkedIn</a>` : ''}
            </figcaption>
        `;
        const io = new IntersectionObserver((entries) => {
            if (entries.some(e => e.isIntersecting)) {
                io.disconnect();
                this.mount();
            }
        }, { rootMargin: '200px' });
        io.observe(this);
    }

    async mount() {
        const src = this.getAttribute('src');
        if (!src) return;
        try {
            const AP = await loadPlayer();
            AP.create(src, this.shadowRoot.querySelector('.player'), {
                fit: 'width',
                idleTimeLimit: 2,
                poster: 'npt:0:9',
                loop: true,
            });
        } catch (e) {
            this.shadowRoot.querySelector('.player').textContent = 'No se ha podido cargar la grabación.';
        }
    }
}

customElements.define('terminal-cast', TerminalCast);
export default TerminalCast;
