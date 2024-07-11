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
                    padding: 20px;
                }
                h2 {
                    color: var(--head1);
                    border-bottom: 2px solid var(--head1);
                    padding-bottom: 10px;
                }
                ul {
                    list-style-type: none;
                    padding-left: 0;
                }
                li {
                    margin-bottom: 10px;
                    position: relative;
                    padding-left: 20px;
                }
                li::before {
                    content: '▹';
                    color: var(--green);
                    position: absolute;
                    left: 0;
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