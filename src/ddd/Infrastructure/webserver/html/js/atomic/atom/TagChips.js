import { baseSheet, sheet, esc } from '../lib/dom.js';

// <tag-chips> — lista de etiquetas. Recibe los datos por PROPIEDAD (el.tags = [...])
// o por atributo JSON (tags='["PHP","Nix"]').
const own = sheet(`
    ul { display: flex; flex-wrap: wrap; gap: 6px; }
    li {
        font: 500 12px/1 var(--pm-mono);
        padding: 6px 10px; border-radius: 999px;
        color: var(--pm-strong);
        background: color-mix(in srgb, var(--pm-accent) 14%, transparent);
        border: 1px solid color-mix(in srgb, var(--pm-accent) 30%, transparent);
    }
`);

class TagChips extends HTMLElement {
    static observedAttributes = ['tags'];

    constructor() {
        super();
        this.attachShadow({ mode: 'open' }).adoptedStyleSheets = [baseSheet, own];
        this._tags = [];
    }

    set tags(list) { this._tags = list || []; this.render(); }
    get tags() { return this._tags; }

    attributeChangedCallback(_, __, value) {
        try { this.tags = JSON.parse(value || '[]'); } catch { this.tags = []; }
    }

    render() {
        this.shadowRoot.innerHTML = `<ul>${this._tags.map(t => `<li>${esc(t)}</li>`).join('')}</ul>`;
    }
}

customElements.define('tag-chips', TagChips);
export default TagChips;
