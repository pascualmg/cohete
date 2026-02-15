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
                margin: 0 auto;
                padding: 20px;
                background: var(--head2-bg);
                border-radius: 20px;
                border: 2px solid var(--border);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            }
            .technologies-title {
                text-align: center;
                font-weight: bold;
                font-size: 1.5em;
                margin-bottom: 25px;
                color: var(--head1);
                text-transform: uppercase;
                letter-spacing: 1.5px;
                border-bottom: 2px solid var(--green);
                padding-bottom: 12px;
                position: relative;
            }
            .technologies-title::after {
                content: '';
                position: absolute;
                bottom: -3px;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 3px;
                background: var(--comp);
                border-radius: 2px;
            }
            .technologies-list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                gap: 15px;
                padding: 0;
                list-style: none;
            }
            .technologies-list li {
                flex: 0 0 auto;
                background: var(--bg1);
                border: 2px solid var(--border);
                border-radius: 12px;
                padding: 12px;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
            }
            .technologies-list li::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, var(--green), var(--comp));
                opacity: 0;
                transition: all 0.3s ease;
                z-index: 1;
            }
            .technologies-list li:hover::before {
                opacity: 0.1;
            }
            .technologies-list li:hover {
                transform: translateY(-8px) scale(1.05);
                box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
                border-color: var(--green);
            }
            .technologies-list img {
                width: 40px;
                height: 40px;
                object-fit: contain;
                transition: all 0.3s ease;
                position: relative;
                z-index: 2;
                filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.2));
            }
            .technologies-list li:hover img {
                transform: scale(1.1);
                filter: drop-shadow(0 4px 15px rgba(0, 0, 0, 0.2));
            }
            @media (min-width: 480px) {
                .technologies-container {
                    padding: 25px;
                    border-radius: 22px;
                }
                .technologies-title {
                    font-size: 1.6em;
                    margin-bottom: 28px;
                    padding-bottom: 13px;
                }
                .technologies-list {
                    gap: 18px;
                }
                .technologies-list li {
                    padding: 14px;
                    border-radius: 14px;
                }
                .technologies-list img {
                    width: 45px;
                    height: 45px;
                }
            }

            @media (min-width: 768px) {
                .technologies-container {
                    padding: 30px;
                    border-radius: 25px;
                }
                .technologies-title {
                    font-size: 1.8em;
                    margin-bottom: 30px;
                    padding-bottom: 15px;
                    letter-spacing: 2px;
                    border-bottom: 3px solid var(--green);
                }
                .technologies-list {
                    gap: 20px;
                }
                .technologies-list li {
                    padding: 15px;
                    border-radius: 15px;
                }
                .technologies-list img {
                    width: 50px;
                    height: 50px;
                }
            }

            @media (min-width: 1024px) {
                .technologies-container {
                    padding: 35px;
                }
                .technologies-title {
                    font-size: 2em;
                    margin-bottom: 35px;
                }
                .technologies-list {
                    gap: 25px;
                }
                .technologies-list li {
                    padding: 18px;
                }
                .technologies-list img {
                    width: 55px;
                    height: 55px;
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