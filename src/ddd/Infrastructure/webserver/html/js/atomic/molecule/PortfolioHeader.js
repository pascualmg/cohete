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
                    padding: 30px 15px;
                    text-align: center;
                    justify-content: center;
                    background: linear-gradient(135deg, var(--bg1), var(--head2-bg));
                    border-radius: 20px;
                    margin: 10px;
                    border: 2px solid var(--border);
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
                    position: relative;
                    overflow: hidden;
                }
                
                .portfolio-header::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: radial-gradient(circle, rgba(0, 255, 136, 0.1) 0%, transparent 50%);
                    animation: rotate 20s linear infinite;
                    z-index: 0;
                }
                
                .portfolio-header > * {
                    position: relative;
                    z-index: 1;
                }
                
                .header-photo img {
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-bottom: 15px;
                    border: 3px solid var(--green);
                    box-shadow: 0 6px 20px rgba(0, 255, 136, 0.3);
                    transition: all 0.3s ease;
                    animation: profilePulse 3s ease-in-out infinite;
                }
                
                .header-photo img:hover {
                    transform: scale(1.05);
                    box-shadow: 0 12px 35px rgba(0, 255, 136, 0.5);
                }

                .header-title {
                    color: var(--head1);
                    font-size: 1.6rem;
                    margin-bottom: 12px;
                    font-weight: bold;
                    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
                    animation: slideInDown 1s ease-out;
                    line-height: 1.2;
                }

                .header-title-2 {
                    color: var(--green);
                    font-size: 1.2rem;
                    margin-bottom: 12px;
                    font-weight: 600;
                    text-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
                    animation: slideInLeft 1s ease-out 0.3s both;
                    line-height: 1.3;
                }

                .header-title-3 {
                    color: var(--comp);
                    font-size: 1rem;
                    font-style: italic;
                    animation: slideInRight 1s ease-out 0.6s both;
                    line-height: 1.4;
                }
                
                @keyframes rotate {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                
                @keyframes profilePulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.02); }
                }
                
                @keyframes slideInDown {
                    from {
                        opacity: 0;
                        transform: translateY(-30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                @keyframes slideInLeft {
                    from {
                        opacity: 0;
                        transform: translateX(-30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
                
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }

                @media (min-width: 480px) {
                    .portfolio-header {
                        padding: 35px 20px;
                        margin: 15px;
                        border-radius: 25px;
                    }

                    .header-photo img {
                        width: 140px;
                        height: 140px;
                        margin-bottom: 18px;
                        border: 4px solid var(--green);
                    }

                    .header-title {
                        font-size: 1.8rem;
                        margin-bottom: 14px;
                    }

                    .header-title-2 {
                        font-size: 1.4rem;
                        margin-bottom: 14px;
                    }

                    .header-title-3 {
                        font-size: 1.1rem;
                    }
                }

                @media (min-width: 768px) {
                    .portfolio-header {
                        padding: 50px 40px;
                        margin: 20px;
                        border-radius: 30px;
                    }

                    .header-photo img {
                        width: 180px;
                        height: 180px;
                        margin-bottom: 20px;
                    }

                    .header-title {
                        font-size: 2.5rem;
                        margin-bottom: 16px;
                    }

                    .header-title-2 {
                        font-size: 1.8rem;
                        margin-bottom: 16px;
                    }

                    .header-title-3 {
                        font-size: 1.3rem;
                    }
                }

                @media (min-width: 1024px) {
                    .portfolio-header {
                        padding: 60px 50px;
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