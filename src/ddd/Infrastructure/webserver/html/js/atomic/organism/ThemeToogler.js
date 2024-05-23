import ThemeSwitcher from "./themeSwitcher.js";

class ThemeToggler extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.themes = ['dark', 'light', 'solarized-dark', 'solarized-light'];
        this.currentThemeIndex = 0;
    }

    connectedCallback() {
        this.render();

        this.themeSwitcher = this.shadowRoot.querySelector('theme-switcher');
        if(this.themeSwitcher === null){
            console.error("no se puedee localizr el theme switcher")
        }
        this.updateTheme();

        this.shadowRoot.querySelector('button').addEventListener('click', () => this.nextTheme());
        this.shadowRoot.querySelector('button').addEventListener('wheel', (e) => this.onScroll(e));
    }

    render() {
        this.shadowRoot.innerHTML = `
        <style>
            :host {
                position: fixed;
                top: 20px; /* Ajusta según sea necesario */
                right: 20px; /* Ajusta según sea necesario */
                z-index: 1000; /* Ajusta según sea necesario */
            }
            
    button {
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        background: linear-gradient(var(--bg1), var(--str));
        color: white; /* O el color de texto que prefieras */
        cursor: pointer;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background 0.3s;
    }
    button:hover {
        /* Puedes definir el hover como desees */
    }
        </style>
        <theme-switcher></theme-switcher>
        <button></button>
    `;
    }

    nextTheme() {
        this.currentThemeIndex = (this.currentThemeIndex + 1) % this.themes.length;
        this.updateTheme();
    }

    onScroll(event) {
        if (event.deltaY < 0) {
            this.currentThemeIndex = (this.currentThemeIndex - 1 + this.themes.length) % this.themes.length;
        } else {
            this.currentThemeIndex = (this.currentThemeIndex + 1) % this.themes.length;
        }
        this.updateTheme();
    }

    updateTheme() {
        const theme = this.themes[this.currentThemeIndex];
        if (this.themeSwitcher) {
            this.themeSwitcher.setAttribute('theme', theme);
        }
    }
}

customElements.define('theme-toggler', ThemeToggler);
export default ThemeToggler;
