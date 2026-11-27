// Utilidades minimas para los Web Components del portfolio.
//
// La idea, en tres piezas:
//  - template(): un <template> es un MOLDE. El navegador lo parsea una vez y no lo pinta.
//  - stamp():    clona el molde en un DocumentFragment (la BANDEJA). Montas ahi todo y
//                lo metes en la pagina de golpe: un solo repintado.
//  - sheet():    una hoja de estilos construible (CSSStyleSheet). Se crea UNA vez y la
//                comparten todos los shadow roots via adoptedStyleSheets, sin duplicar CSS.

export const esc = (v = '') => String(v).replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[c]));

export function template(markup) {
    const t = document.createElement('template');
    t.innerHTML = markup;
    return t;
}

// importNode (y no tpl.content.cloneNode): el contenido de un <template> vive en un
// documento inerte, y ahi los custom elements NO se "despiertan". Importandolo al documento
// real, <tag-chips> y compañia nacen ya mejorados y podemos pasarles datos por propiedad.
export const stamp = (tpl) => document.importNode(tpl.content, true);

export function sheet(css) {
    const s = new CSSStyleSheet();
    s.replaceSync(css);
    return s;
}

export const fmtMonth = (iso) => iso
    ? new Date(iso).toLocaleDateString('es-ES', { month: 'short', year: 'numeric' })
    : 'hoy';

// Estilos base compartidos. Solo usan tokens --pm-*, que define el organismo
// <pascualmg-portfolio>. Cambiar el diseño = cambiar esos tokens.
export const baseSheet = sheet(`
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :host { display: block; color: var(--pm-text); font-family: var(--pm-font); line-height: 1.55; }
    a { color: var(--pm-accent); text-decoration: none; }
    a:hover { text-decoration: underline; }
    ul { list-style: none; }
    .card {
        background: var(--pm-surface);
        border: 1px solid var(--pm-border);
        border-radius: var(--pm-radius);
        padding: 22px;
    }
    .eyebrow {
        font: 600 12px/1.2 var(--pm-mono);
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--pm-accent-2);
    }
    h2, h3, h4 { color: var(--pm-strong); line-height: 1.25; }
    .muted { color: var(--pm-muted); }
    .btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 18px; border-radius: 999px;
        font-weight: 600; font-size: 15px;
        border: 1px solid var(--pm-border); color: var(--pm-strong);
        transition: transform .15s ease, border-color .15s ease;
    }
    .btn:hover { text-decoration: none; transform: translateY(-1px); border-color: var(--pm-accent); }
    .btn.primary { background: var(--pm-accent); color: var(--pm-on-accent); border-color: transparent; }
`);
