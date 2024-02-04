class PortfolioHeader extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.shadowRoot.innerHTML = `
            <style>
                .portfolio-header {
                    background-color: var(--bg1);
                    color: var(--base);
                }

                .header-background-image img {
                    background-color: var(--bg3);
                }

                .header-content {
                    background-color: var(--bg2);
                }

                .header-photo img {
                    border-radius: 50%; /* Hace la imagen circular */
                    object-fit: cover; /* Mantiene las proporciones de la imagen */
                    width: 100px; /* Ancho de la imagen */
                    height: 100px; /* Altura de la imagen */
                }

                .header-content h1 {
                    color: var(--head1);
                }

                .header-content h2 {
                    color: var(--head2);
                }

                .header-content h3 {
                    color: var(--head3);
                }

                .header-social-media {
                    background-color: var(--bg-alt);
                }

                .header-location {
                    color: var(--base-dim);
                }

                /* ...otros estilos... */
            </style>
            <section class="portfolio-header">
                <div class="header-background-image">
                    <img src="your_background_image_url" alt="Background image">
                </div>
                <div class="header-content">
                    <div class="header-photo">
                        <img src="your_profile_picture_url" alt="Profile picture">
                    </div>
                    <h1>Your Name</h1>
                    <h2>Your Professional Title</h2>
                    <h3>Your Subtitle</h3>
                </div>
                <div class="header-social-media">
                    <!-- Social media buttons/icons -->
                </div>
                <div class="header-location">
                    <!-- Your location -->
                </div>
            </section>
        `;
    }
}

customElements.define('portfolio-header', PortfolioHeader);
export default PortfolioHeader