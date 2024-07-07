class PascualmgBlog extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    font-family: 'Hasklig', Arial, sans-serif;
                    padding: 20px;
                    text-align: center;
                }

                h1 {
                    font-size: 24px;
                    margin-bottom: 20px;
                }

                p {
                    font-size: 16px;
                    line-height: 1.5;
                    margin-bottom: 20px;
                }

                .icon {
                    font-size: 48px;
                    margin-bottom: 20px;
                }
            </style>

            <div class="icon">🚧</div>
            <h1>Blog en construcción</h1>
            <p>Estamos trabajando arduamente para traerte contenido interesante y útil muy pronto.</p>
            <p>¡Mantente atento a las actualizaciones!</p>
        `;
    }
}

customElements.define('pascualmg-blog', PascualmgBlog);
export default PascualmgBlog;