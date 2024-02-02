import TechnologiesList  from "../molecule/technologiesList.js";
import ChatBox  from "./chat-box.js";

class PortfolioElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});

    }
    connectedCallback() {
        const theme = this.getAttribute('theme') || 'dark';
        // language=HTML
        this.shadowRoot.innerHTML = `
            <style>
                /* Colores Solarized */
                :host {
                    --solarized-dark0: #002b36;
                    --solarized-dark1: #073642;
                    --solarized-dark2: #586e75;
                    --solarized-dark3: #657b83;
                    --solarized-dark4: #839496;
                    --solarized-light0: #fdf6e3;
                    --solarized-light1: #eee8d5;
                    --solarized-light2: #93a1a1;
                    --solarized-light3: #839496;
                    --solarized-light4: #657b83;
                }

                /* Estilos generales */
                body {
                    background-color: var(--solarized-${theme}0);
                    color: var(--solarized-${theme === 'dark' ? 'light' : 'dark'}0);
                    font-family: Arial, sans-serif;
                }

                header {
                    background-color: var(--solarized-${theme}1);
                    padding: 10px;
                    color: var(--solarized-${theme === 'dark' ? 'light' : 'dark'}0);
                    text-align: center;
                }

                section {
                    background-color: var(--solarized-${theme === 'dark' ? 'light' : 'dark'}1);
                    color: var(--solarized-${theme}1);
                    margin: 10px;
                    padding: 10px;
                }
            </style>
            <header>
                <h1>Pascual Muñoz Galián</h1>
                <h2>Desarrollador </h2>
            </header>

            <section>
                <h3>Sobre mí</h3>
                <p>Aquí es donde puedes añadir un resumen breve y conciso sobre ti, tus habilidades y tu experiencia
                    profesional.</p>
            </section>

            <section>
                <h3>Proyectos</h3>
                <p>Aquí puedes enumerar tus proyectos más relevantes o impresionantes, con enlaces a los repositorios si
                    están disponibles.</p>
            </section>

            <section>
                <h3>Contacto</h3>
                <p>Indica cómo alguien puede ponerte en contacto. Recuerda, no compartas información de contacto que no
                    quieras que sea pública.</p>
            </section>
            <technologies-list
                    images="
                    https://raw.githubusercontent.com/reactphp/branding/main/reactphp-logo.svg,
                    https://symfony.com/logos/symfony_black_03.png,
                    https://chuidiang.org/images/f/f3/Logo_php.png,
                    https://seeklogo.com/images/G/github-logo-2E3852456C-seeklogo.com.png 
                    "
            >
            </technologies-list>
            <chat-box
                    group="tbl001"
                    host="0.0.0.0"
                    port="8001"
            ></chat-box>


        `;
    }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement