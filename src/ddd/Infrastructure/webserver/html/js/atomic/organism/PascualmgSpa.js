import './pascualmg-portfolio.js';
import './ThemeToogler.js';
import '../molecule/SocialLinks.js';

class PascualmgSpa extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML = `
                <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                :host {
                    font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
                    display: grid;
                    grid-template-columns: 1fr;
                    grid-template-rows: auto 1fr auto;
                    grid-template-areas:
                        "header"
                        "main"
                        "footer";
                    min-height: 100vh;
                    background-color: var(--bg1);
                    color: var(--base);
                }

                .header {
                    grid-area: header;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 15px;
                    background-color: var(--bg2);
                }

                .app-title {
                    font-size: 24px;
                    font-weight: bold;
                    text-align: center;
                    flex-grow: 1;
                }

                .menu-button {
                    background: none;
                    border: none;
                    cursor: pointer;
                    font-size: 24px;
                    color: var(--base);
                    margin-right: 15px;
                    z-index: 101;
                }

                .main-content {
                    grid-area: main;
                    padding: 20px;
                }

                .footer {
                    grid-area: footer;
                    padding: 15px;
                    text-align: center;
                    background-color: var(--bg2);
                }

                .sidebar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    width: 250px;
                    padding: 60px 20px 20px;
                    background-color: var(--bg3);
                    transform: translateX(-100%);
                    transition: transform 0.3s ease-in-out;
                    z-index: 100;
                    border-top-right-radius: 20px;
                    border-bottom-right-radius: 20px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }

                .sidebar.open {
                    transform: translateX(0);
                }

                .sidebar ul {
                    list-style-type: none;
                }

                .sidebar ul li {
                    margin-bottom: 15px;
                    padding: 10px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                    border-radius: 5px;
                }

                .sidebar ul li:hover {
                    background-color: var(--bg2);
                }
            </style>

            <header class="header">
                <button class="menu-button" id="menu-button">&#9776;</button>
                <div class="app-title">Pascualmg.dev</div>
                <theme-toggler></theme-toggler>
            </header>

            <aside class="sidebar" id="sidebar">
                <ul>
                    <li data-custom-component-name="pascualmg-portfolio">Sobre mi</li>
                    <li data-href="/blog">Blog</li>
                </ul>
            </aside>

            <main class="main-content" id="main-content">
                <pascualmg-portfolio></pascualmg-portfolio>
            </main>

            <footer class="footer">
                <social-links links='[
                           {"name": "phone", "url": "tel:+34693000983"},
                           {"name": "email", "url": "mailto:info@pascualmg.dev"},
                           {"name": "linkedin", "url": "https://www.linkedin.com/in/pascual-mu%C3%B1oz-gali%C3%A1n-7b4b3896/"},
                           {"name": "github", "url": "https://github.com/pascualmg"},
                           {"name": "twitter", "url": "https://twitter.com/pascualmg"}
                           ]'>
                </social-links>
            </footer>
        `;

        this.toggleMenu = this.toggleMenu.bind(this);
        this.LoadCustomComponent = this.LoadCustomComponent.bind(this);
    }

    connectedCallback() {
        const menuButton = this.shadowRoot.querySelector('#menu-button');
        const sidebar = this.shadowRoot.querySelector('#sidebar');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        this.shadowRoot.querySelectorAll('.sidebar ul li').forEach(item => {
            item.addEventListener('click', (event) => {
                const href = event.target.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                    return;
                }
                const customComponentName = event.target.getAttribute('data-custom-component-name');
                const mainContent = this.shadowRoot.querySelector('#main-content');

                if (customComponentName) {
                    mainContent.innerHTML = `<${customComponentName}></${customComponentName}>`;
                    sidebar.classList.remove('open');
                }
            });
        });
    }

    disconnectedCallback() {
        const menuButton = this.shadowRoot.querySelector('#menu-button');
        const sidebar = this.shadowRoot.querySelector('#sidebar');

        menuButton.removeEventListener('click', this.toggleMenu);
        this.shadowRoot.querySelectorAll('.sidebar ul li').forEach(item => {
            item.removeEventListener('click', this.LoadCustomComponent);
        });
    }

    toggleMenu() {
        const sidebar = this.shadowRoot.querySelector('#sidebar');
        sidebar.classList.toggle('open');
    }

    LoadCustomComponent(event) {
        const customComponentName = event.target.getAttribute('data-custom-component-name');
        const mainContent = this.shadowRoot.querySelector('#main-content');

        if (customComponentName) {
            mainContent.innerHTML = `<${customComponentName}></${customComponentName}>`;
            const sidebar = this.shadowRoot.querySelector('#sidebar');
            sidebar.classList.remove('open');
        } else {
            console.error('Custom component name not found');
        }
    }
}

customElements.define('spa-pascualmg', PascualmgSpa);