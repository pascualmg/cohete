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
                    height: auto;
                    background-repeat: round; 
                    margin: 10px;
                    padding: 10px;
                    border-radius: 10px;
                    border: 1px solid var(--border);
                    text-align: center;
                } 
                .header-photo img {
                    border-radius: 50%; 
                    object-fit: cover; 
                    width: 150px; 
                    height: 150px; 
                    margin-bottom: 10px;
                }
                .header-title {
                    color: var(--aqua); 
                }
                .header-title-2 {
                    color: var(--head2); 
                }
                .header-title-3 {
                    color: var(--head3); 
                }
                .header-location img {
                    width: 30px; 
                    height: 30px; 
                    vertical-align: middle;
                }
                .header-location {
                    color: var(--magenta);
                    margin-top: 10px;
                }
            </style>
            <section class="portfolio-header">
                <div class="header-photo">
                    <img src="https://raw.githubusercontent.com/pascualmg/cdn/main/me.png" alt="Profile picture">
                </div>
                <h1 class="header-title">Pascual Muñoz Galián</h1>
                <h2 class="header-title-2">PHP Backend <changing-text texts='[
                        "junior",
                        "senior"   
                        ]' ></changing-text> Developer</h2>
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
                        "ReactPHP noob"
                        ]' 
                        delay="1"
                    ></changing-text>
                </h3>
                <div class="header-location">
                    <span>Location: Earth</span>
                </div>
            </section>
        `;

        const portfolioHeader = this.shadowRoot.querySelector('.portfolio-header');
        parallaxBackground(
            portfolioHeader,
            'https://raw.githubusercontent.com/pascualmg/cdn/main/header-background.png',
            0.2,
            480,
            "50% 50%"
        );
    }
}

function parallaxBackground(elem, backgroundImageURL, scrollSpeed, viewportWidth, backgroundPosition = '50% 50%') {
    const parallaxImage = elem;
    parallaxImage.style.backgroundImage = `url(${backgroundImageURL})`;
    parallaxImage.style.backgroundPosition = backgroundPosition;

    const parallaxTick = () => {
        if (window.innerWidth > viewportWidth) {
            parallaxImage.style.transform = `translateX(${window.scrollY * scrollSpeed * 10}px)`;
            parallaxImage.style.opacity = `${100.0 / window.scrollY}`;
        } else {
            parallaxImage.style.transform = 'none';
        }
    }

    window.addEventListener("scroll", () => {
        window.requestAnimationFrame(parallaxTick)
    });

    window.addEventListener("resize", () => {
        window.requestAnimationFrame(parallaxTick)
    });
}

customElements.define('portfolio-header', PortfolioHeader);
export default PortfolioHeader;
