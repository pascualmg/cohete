class TechnologiesList extends HTMLElement {

    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        // Get the list of images from the 'images' attribute.
        const images = JSON.parse(this.getAttribute('images'));

        // Create a <li> element for each image.
        let imageElements = '';

        images.forEach((image, index) => {
            imageElements += `
                <li>
                    <a><img src="${image.src}" alt="${image.alt}"></a>
                </li>
            `;
        });

        this.shadowRoot.innerHTML = `
<style>
    .technologies-container {
        margin: auto;
        max-width: 1200px;
        padding: 1em;
    }
    .technologies-title, .technologies-subtitle {
        text-align: center;
    }
    .technologies-title {
        font-weight: 600;
        margin-bottom: 1em;
    }
    .technologies-list {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-around;
        padding: 0;
        list-style: none;
    }
    .technologies-list li {
        flex: 1 0 20%; // for larger screens, gives approximately 5 items per row
        margin: 1em;
        max-width: 50%; // for smaller screens, 2 items per row
    }
    .technologies-list img {
        width: 100%; /* limit width to 100% of the parent element */
        height: auto; /* to maintain aspect ratio */
        max-width: 80px; /* limit the maximum width */
        max-height: 80px; /* limit the maximum height */
        object-fit: contain; /* this will make the images scale to fit inside their box */
    }
    @media screen and (min-width: 600px) {
        .technologies-list li {
            max-width: 25%; 
        }
    }
</style>
            <div class="technologies-container">
                <h2 class="technologies-title">El stack de tecnologías que utilizo, amadas u odiadas sin distinción 👽</h2>
                <ul class="technologies-list">
                    ${imageElements}
                </ul>
            </div>
        `;
    }
}

window.customElements.define('technologies-list', TechnologiesList);
export default TechnologiesList