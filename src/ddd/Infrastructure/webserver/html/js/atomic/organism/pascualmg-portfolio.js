import TechnologiesList from "../molecule/technologiesList.js";
import ChatBox from "./chat-box.js";
import PortFolioHeader from "../molecule/PortfolioHeader.js"

class PortfolioElement extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });

  }
  connectedCallback() {
    this.shadowRoot.innerHTML = `
            <style>
                /* Estilos generales */
                body {
                    background-color: var(--bg1);
                    color: var(--base);
                    font-family: Arial, sans-serif;
                }

                header {
                    background-color: var(--bg1);
                    padding: 10px;
                    color: var(--head1);
                    text-align: center;
                }

                h2 {
                color: var(--head2)
                }
                h3 {
                    color: var(--head3)
                }

                section {
                    background-color: var(--bg3);
                    color: var(--head2);
                    margin: 10px;
                    padding: 10px;
                    border-radius: 10px;
                    border: 1px solid var(--border);
                }
            </style>
           <portfolio-header></portfolio-header>

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
                    hidden
                    group="tbl001"
                    host="pascualmg.dev"
                    port="8001"
            ></chat-box>


        `;
  }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement
