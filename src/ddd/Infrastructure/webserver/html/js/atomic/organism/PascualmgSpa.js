import './pascualmg-portfolio.js';
import './ThemeToogler.js';
import './ChatBox.js';
import '../molecule/SocialLinks.js'
/**
  @extends HTMLElement
 * @property {Function} toggleMenu - Muestra u oculta la barra lateral segun si es movil o no.
 * @property {Function} LoadCustomComponent - Carga el Web Component en el main-content.
 *
 * @property {Function} connectedCallback - Método que se ejecuta cuando el componente se conecta al DOM
 * @property {Function} disconnectedCallback - Método que se ejecuta cuando el componente se desconecta del DOM
 *
 * @example
 * <spa-pascualmg>😂</spa-pascualmg>
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/Web_Components
 * Este componente es un ejemplo de una SPA (Single Page Application) que utiliza Web Components para crear una aplicación web.
 * Se compone de un encabezado, una barra lateral y un contenido principal.
 * La barra lateral contiene una lista de enlaces a diferentes partes de la aplicación.
 * El contenido principal se actualiza cuando se hace clic en un enlace de la barra lateral.
 * Para ello, solamente hay que incluir el nombre del componente personalizado en el atributo data-custom-component-name
 * de la etiqueta <li> de la barra lateral.
 *
 * Automáticamente, se cargará el contenido del componente personalizado en el contenido principal de forma dinámica.
 *

 */
class PascualmgSpa extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML =`
<style>
    @font-face {
        font-family: Hasklig;
        src: url('https://raw.githubusercontent.com/pascualmg/cdn/fonts/Hasklig/HasklugNerdFont-Regular.otfg') format('opentype');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
    }

    /* Restablecer el estilo por defecto del navegador */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Estilo general */
    :host {
        font-family: 'Hasklig', Arial, sans-serif;
        display: block;
    }


    header, footer {
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 10px 0;
    }

    /* Estilo del contenedor principal */
    .container {
        display: flex;
        flex-direction: column; /* Mobile first: Columna por defecto */
    }

    .menu-button {
        display: block;
        background-color: #333;
        color: #fff;
        border: none;
        padding: 10px;
        text-align: left;
        font-size: 16px;
        cursor: pointer;
    }

    .sidebar {
        display: none; /* Oculta la barra lateral por defecto en móviles */
        background-color: var(--base);
        padding: 20px;
        position: absolute;
    }

    .sidebar ul {
        list-style-type: none;
    }

    .sidebar ul li {
        margin-bottom: 10px;
        cursor: pointer;
    }

    .main-content {
        padding: 20px;
        flex: 1;
    }

    theme-toogler {
        /* cuando esta en la version movil se hace mas grande para que sea pulsable por un dedo */
        width: 500px;
        height: 500px;
    }

    /* Media Queries para pantallas más grandes */
   
</style>
<header class="header">
    <theme-toggler></theme-toggler>
    <h1>Pascualmg.dev</h1>
</header>
<div class="container">
    <button class="menu-button" id="menu-button">&#9776; Menu</button>
    <aside class="sidebar" id="sidebar">
        <!-- Contenido de la barra lateral -->
        <ul>
            <li data-custom-component-name="pascualmg-portfolio">Sobre mi</li>
            <li data-custom-component-name="Foo">Parte 2</li>
            <li data-custom-component-name="chat-box">chat</li>
            <!-- Más partes -->
        </ul>
    </aside>
    <main class="main-content" id="main-content">
        <pascualmg-portfolio></pascualmg-portfolio>
    </main>
</div>
<footer class="footer">
    <social-links links='[
         {"name": "twitter", "url": "https://twitter.com/pascualmg"},
         {"name": "github", "url": "https://github.com/pascualmg"},
         {"name": "linkedin", "url": "https://www.linkedin.com/in/pascual-mu%C3%B1oz-gali%C3%A1n-7b4b3896/"},
         {"name": "facebook", "url": "https://www.facebook.com/pascualmg"},
         {"name": "email", "url": "mailto:info@pascualmg.dev"},
         {"name": "phone", "url": "tel:+34693000983"}
         ]'></social-links>

</footer>
        `;

        // Bind methods
        this.toggleMenu = this.toggleMenu.bind(this);
        this.LoadCustomComponent = this.LoadCustomComponent.bind(this);
    }

    connectedCallback() {
        this.shadowRoot.querySelector('#menu-button').addEventListener('click', this.toggleMenu);
        this.shadowRoot.querySelectorAll('.sidebar ul li').forEach(item => {
            item.addEventListener('click', this.LoadCustomComponent);
        });
    }

    disconnectedCallback() {
        this.shadowRoot.querySelector('#menu-button').removeEventListener('click', this.toggleMenu);
        this.shadowRoot.querySelectorAll('.sidebar ul li').forEach(item => {
            item.removeEventListener('click', this.LoadCustomComponent);
        });
    }

    toggleMenu() {
        const sidebar = this.shadowRoot.querySelector('#sidebar');
        sidebar.style.display = sidebar.style.display === 'block' ? 'none' : 'block';
    }

    /**
     * @param {Event} event
     */
    LoadCustomComponent(event) {
        const customComponentName = event.target.getAttribute('data-custom-component-name');
        const mainContent = this.shadowRoot.querySelector('#main-content');

        if(!customComponentName){
            console.error('Custom component name not found');
            return;
        }

        mainContent.innerHTML = `<${customComponentName}></${customComponentName}>`;

        // Ocultar menú después de la selección en móviles
        if (window.innerWidth < 768) {
            this.toggleMenu();
        }
    }
}

customElements.define('spa-pascualmg', PascualmgSpa);
