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
    } 
   
   .header-content {
    }
    .header-social-media {
        background-color: var(--bg-alt);
    }
    .header-location {
        color: var(--magenta);
    }
    .header-photo img {
        border-radius: 50%; 
        object-fit: cover; 
        width: 200px; 
        height: 200px; 
    }
   

    
    .header-title {
        color: var(--aqua); /* Color de los títulos */
    }
     .header-title-2 {
        color: var(--head2); /* Color de los títulos */
    }
     .header-title-3 {
        color: var(--head3); /* Color de los títulos */
    }
    
    .centeredText {
        text-align: center;
    }
    
</style>
<section class="portfolio-header">
    <div class="header-content">
        <div class="header-photo">
            <img src="https://raw.githubusercontent.com/pascualmg/cdn/main/me.png" alt="Profile picture">
        </div>
        <h1 class="header-title centeredText">PascualMG</h1>
        <h2 class="header-title-2 centeredText">PHP Developer</h2>
        <h3 class="header-title-3 centeredText" >and GW-BASIC junior</h3>
    </div>
    <div class="header-social-media">
        <!-- Social media buttons/icons -->
atomos para los botones de las rss 
    </div>
    <div class="header-location">
        <img src="https://cdn.wikimg.net/en/chronowiki/images/2/2d/EarthMan.gif" alt="earthbound"></>
    </div>
</section>`
        const portfolioHeader = this.shadowRoot.querySelector('.portfolio-header');
        parallaxBackground(
            portfolioHeader,
            'https://raw.githubusercontent.com/pascualmg/cdn/main/header-background.png',
            0.2,
            480,
            "50% 50%"
        )
    }
}


/**
 * based  https://godotengine.org/ with LLM y me ha gustado mucho :)
 * Sets up a parallax effect for a given element with a background image.
 *
 * @param {HTMLElement} elem - The element to apply the parallax effect on.
 * @param {string} backgroundImageURL - The URL of the background image.
 * @param {number} scrollSpeed - The speed of the parallax effect.
 * @param {number} viewportWidth - The width of the viewport.
 * @param {string} [backgroundPosition='50% 50%'] - The background position property.
 */
function parallaxBackground(elem, backgroundImageURL, scrollSpeed, viewportWidth, backgroundPosition = '50% 50%') {
    const parallaxImage = elem;
    parallaxImage.style.backgroundImage = `url(${backgroundImageURL})`;
    parallaxImage.style.backgroundPosition = backgroundPosition;

    const parallaxTick = () => {
        if (window.innerWidth > viewportWidth) {
            parallaxImage.style.transform = `translateY(${window.scrollY * scrollSpeed * 10}px)`;
            parallaxImage.style.opacity = `${100.0 / window.scrollY}`;
        } else {
            parallaxImage.style.transform = 'none';
        }
    }

    // Event listeners para scroll y resize
    window.addEventListener("scroll", () => {
        window.requestAnimationFrame(parallaxTick)
    });

    window.addEventListener("resize", () => {
        window.requestAnimationFrame(parallaxTick)
    });
}


customElements.define('portfolio-header', PortfolioHeader);
export default PortfolioHeader