class SoftSkills extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.render();
    }

    render() {
        const skills = JSON.parse(this.getAttribute('skills') || '[]');

        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    color: var(--base);
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 15px;
                    background: var(--head2-bg);
                    border-radius: 15px;
                    border: 1px solid var(--border);
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                }
                h2 {
                    color: var(--head1);
                    border-bottom: 2px solid var(--green);
                    padding-bottom: 12px;
                    margin-bottom: 20px;
                    text-align: center;
                    font-size: 1.3em;
                    text-transform: uppercase;
                    letter-spacing: 1.5px;
                }
                ul {
                    list-style-type: none;
                    padding-left: 0;
                    display: grid;
                    gap: 12px;
                }
                li {
                    position: relative;
                    padding: 12px 15px 12px 40px;
                    background: var(--bg1);
                    border-radius: 10px;
                    border: 1px solid var(--border);
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                    line-height: 1.4;
                    font-size: 0.9em;
                }
                li:hover {
                    transform: translateX(10px);
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                    border-color: var(--green);
                    background: linear-gradient(135deg, var(--bg1), var(--head2-bg));
                }
                li::before {
                    content: '💡';
                    position: absolute;
                    left: 12px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 1.1em;
                    transition: all 0.3s ease;
                }
                li:hover::before {
                    transform: translateY(-50%) scale(1.2);
                }
                @keyframes slideInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                li {
                    animation: slideInUp 0.6s ease-out forwards;
                }
                li:nth-child(1) { animation-delay: 0.1s; }
                li:nth-child(2) { animation-delay: 0.2s; }
                li:nth-child(3) { animation-delay: 0.3s; }
                li:nth-child(4) { animation-delay: 0.4s; }
                li:nth-child(5) { animation-delay: 0.5s; }
                li:nth-child(6) { animation-delay: 0.6s; }

                @media (min-width: 480px) {
                    :host {
                        padding: 18px;
                        border-radius: 18px;
                    }

                    h2 {
                        font-size: 1.4em;
                        padding-bottom: 14px;
                        margin-bottom: 22px;
                    }

                    ul {
                        gap: 14px;
                    }

                    li {
                        padding: 14px 18px 14px 45px;
                        border-radius: 12px;
                        font-size: 0.95em;
                    }

                    li::before {
                        left: 14px;
                        font-size: 1.15em;
                    }
                }

                @media (min-width: 768px) {
                    :host {
                        padding: 20px;
                        border-radius: 20px;
                    }

                    h2 {
                        font-size: 1.5em;
                        padding-bottom: 15px;
                        margin-bottom: 25px;
                        letter-spacing: 2px;
                    }

                    ul {
                        gap: 15px;
                    }

                    li {
                        padding: 15px 20px 15px 50px;
                        font-size: 1em;
                    }

                    li::before {
                        left: 15px;
                        font-size: 1.2em;
                    }
                }
            </style>
            
            <h2>SKILLS</h2>
            <ul>
                ${skills.map(skill => `<li>${skill}</li>`).join('')}
            </ul>
 
        `;
    }
}

customElements.define('soft-skills', SoftSkills);
export default SoftSkills;