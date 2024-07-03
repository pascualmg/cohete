import ChangingText from "../atom/ChangingText.js";

class PortfolioHeader extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.shadowRoot.innerHTML = `
            <style>
                .portfolio-header {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 20px;
                    text-align: center;
                    justify-content: center;
                }
                
                .header-photo img {
                    width: 150px;
                    height: 150px;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-bottom: 20px;
                    border: 4px solid var(--blue);
                }

                .header-title {
                    color: var(--cyan);
                    font-size: 2rem;
                    margin-bottom: 10px; 
                }

                .header-title-2 {
                    color: var(--green);
                    font-size: 1.5rem;
                    margin-bottom: 10px;
                }

                .header-title-3 {
                    color: var(--yellow);
                    font-size: 1.2rem;
                }

                @media (min-width: 768px) {
                    .portfolio-header {
                        padding: 50px 50px 0;
                    }

                    .header-photo img {
                        width: 200px;
                        height: 200px;
                    }

                    .header-title {
                        font-size: 3rem;
                    }

                    .header-title-2 {
                        font-size: 2rem;
                    }

                    .header-title-3 {
                        font-size: 1.5rem;
                    }
                }
            </style>

            <section class="portfolio-header">
                <div class="header-photo">
                    <img src="https://raw.githubusercontent.com/pascualmg/cdn/main/me.png" alt="Profile picture">
                </div>
                <h1 class="header-title">
                    <changing-text texts='[
                        "¡Hola! Soy ",
                        "Hello! Im ",
                        "こんにちは！私は ",
                        "Hallo! Ich bin ",
                        "Ciao! Sono ",
                        "Olá! Eu sou ",
                        "Привет! Я ",
                        "Salut! Sunt ",
                        "Hej! Jeg er ",
                        "Hallå! Jag är ",
                        "Hei! Jeg er ",
                        "Hoi! Ik ben ",
                        "Merhaba! Ben "
                    ]'></changing-text>
                    Pascual Muñoz Galián
                </h1>
                <h2 class="header-title-2">
                    PHP Backend <changing-text texts='["junior", "senior"]'></changing-text> Developer
                </h2>
                <h3 class="header-title-3">
                    <changing-text
                        texts='[
                            "GW-BASIC junior",
                            "Haskell Lover",
                            "You-Dont-Know-JS Fan",
                            "TDD maker",
                            "Curl suffer", 
                            "Extreme Programmer",
                            "Creator of cohete",
                            "ReactPHP noob",
                            "PHP craftsman",
                            "Clean Coder"
                        ]'
                        delay="1"
                    ></changing-text>
                </h3>
            </section>
        `;
    }
}

customElements.define('portfolio-header', PortfolioHeader);
export default PortfolioHeader;