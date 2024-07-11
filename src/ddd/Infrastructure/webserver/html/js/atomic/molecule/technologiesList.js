class TechnologiesList extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        const images = JSON.parse(this.getAttribute('images'));

        let imageElements = '';
        images.forEach((image) => {
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
                max-width: 1000px;
                padding: 1em;
            }
            .technologies-title {
                text-align: center;
                font-weight: 600;
                margin-bottom: 0.5em;
            }
            .technologies-list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                gap: 10px;
                padding: 0;
                list-style: none;
            }
            .technologies-list li {
                flex: 0 0 auto;
                margin: 5px;
            }
            .technologies-list img {
                width: 40px;
                height: 40px;
                object-fit: contain;
                transition: transform 0.2s ease-in-out;
            }
            .technologies-list img:hover {
                transform: scale(1.1);
            }
            @media screen and (max-width: 600px) {
                .technologies-list img {
                    width: 30px;
                    height: 30px;
                }
            }
        </style>
        <div class="technologies-container">
            <h2 class="technologies-title">loved techs 👽</h2>
            <ul class="technologies-list">
                ${imageElements}
            </ul>
        </div>
        `;
    }
}

window.customElements.define('technologies-list', TechnologiesList);
export default TechnologiesList;