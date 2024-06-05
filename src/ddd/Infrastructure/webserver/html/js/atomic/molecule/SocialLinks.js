class SocialLinks extends HTMLElement {
    connectedCallback() {
        this.attachShadow({ mode: 'open' });

        const socialLinks = this.getAttribute('social_links') || '[]';
        const links = JSON.parse(socialLinks);

        const icons = {
            "twitter": "data:image/png;base64,iVBORw0KGg...", // reemplaza esto con tu imagen Base64
            "github": "data:image/png;base64,iVBORw0KGg...", // reemplaza esto con tu imagen Base64
            "linkedin": "data:image/png;base64,iVBORw0KGg..." // reemplaza esto con tu imagen Base64
            // Añade aquí las demás redes sociales
        };

        let html = '';
        links.forEach(link => {
            const icon = icons[link.name];
            if (!icon) {
                console.warn(`Social media not found "${link.name}"`);
                console.warn(`Available social media: ${Object.keys(icons).join(', ')}`)
                return;
            }

            html += `
                <p>
                    <a href="${link.url}">
                        <img src="${icon}" alt="${link.name}">
                        ${link.name}
                    </a>
                </p>
            `;
        });

        this.shadowRoot.innerHTML = html;
    }
}

window.customElements.define('social-links', SocialLinks);