import ThemeSwitcher from "./themeSwitcher.js";

class ThemeToggler extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.currentThemeIndex = 0;
        this.defaultTheme = 'linkedin';

    }

    connectedCallback() {
        this.render();
        this.themeSwitcher = this.shadowRoot.querySelector('theme-switcher');
        this.themes = this.themeSwitcher.themeNames;

        if (!this.themeSwitcher) {
            console.error("No se puede localizar el theme switcher");
        }

        this.themeSwitcher.setAttribute('theme', this.defaultTheme);

        this.shadowRoot.querySelector('button').addEventListener('click', () => this.nextTheme());
    }

    render() {
        this.shadowRoot.innerHTML = `
        <style>
            :host {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 2;
            }

            button {
                border-radius: 15px;
                border: none;
                height: 60px;
                background: linear-gradient(var(--bg1), var(--str));
                color: white;
                cursor: pointer;
                font-size: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                transition: transform 0.3s ease-in-out;
                overflow: hidden;
                z-index: 2;
                min-width: 60px;
                opacity: 0.3;
                
            }
            /* media query para cuando sea version escritorio que se muestre arriba a derecha  */
            
            @media (min-width: 50em) {
                :host {
                    border-radius: 10px;
                    position: fixed;
                    top: 20px;
                    right: 20px;
                 
                }
               
            }
            
                button:hover {
                transform: scale(1.3);
                opacity: 1;
            }

            button:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.3);
            }
        </style>
        <theme-switcher></theme-switcher>
        <button aria-label="Toggle theme"></button>
    `;
    }

    nextTheme() {
        this.currentThemeIndex = (this.currentThemeIndex + 1) % this.themes.length;
        this.updateTheme();
    }


    updateTheme() {
        const theme = this.themes[this.currentThemeIndex];
        if (this.themeSwitcher) {
            this.themeSwitcher.setAttribute('theme', theme);
            this.shadowRoot.querySelector('button').textContent = theme + ' theme';

        }
    }
}

customElements.define('theme-toggler', ThemeToggler);
export default ThemeToggler;