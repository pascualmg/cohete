class ExperienceTimeline extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.render();
    }

    render() {
        const data = JSON.parse(this.getAttribute('data') || '[]');

        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    --timeline-width: 2px;
                    --company-node-size: 24px;
                    --project-node-size: 16px;
                    --company-node-color: var(--head1);
                    --project-node-color: var(--head2);
                    --max-width: 800px;
                }
                .timeline-container {
                    max-width: var(--max-width);
                    margin: 0 auto;
                    padding: 0 15px;
                }
                .timeline {
                    position: relative;
                    padding: 20px 0;
                }
                .timeline::before {
                    content: '';
                    position: absolute;
                    top: 20px;
                    bottom: 0;
                    left: 11px;
                    width: var(--timeline-width);
                    background: var(--company-node-color);
                }
                .experience {
                    position: relative;
                    margin-bottom: 30px;
                    padding-left: 35px;
                }
                .experience::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: var(--company-node-size);
                    height: var(--company-node-size);
                    border-radius: 50%;
                    background: var(--company-node-color);
                    border: 2px solid var(--bg1);
                    z-index: 1;
                }
                .company {
                    font-size: 1.2em;
                    font-weight: bold;
                    color: var(--head1);
                    margin-bottom: 6px;
                    line-height: 1.3;
                }
                .position {
                    font-size: 1em;
                    color: var(--head2);
                    margin-bottom: 4px;
                    line-height: 1.4;
                }
                .date {
                    font-style: italic;
                    color: var(--base-dim);
                    margin-bottom: 15px;
                    font-size: 0.85em;
                    line-height: 1.3;
                }
                .projects {
                    position: relative;
                }
                .projects::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    left: -29px;
                    width: var(--timeline-width);
                    background: var(--project-node-color);
                }
                .project {
                    background: var(--head2-bg);
                    border: 1px solid var(--border);
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 15px;
                    position: relative;
                    transition: all 0.3s ease;
                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
                }
                .project:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                    border-color: var(--green);
                }
                .project::before {
                    content: '';
                    position: absolute;
                    left: -35px;
                    top: 15px;
                    width: var(--project-node-size);
                    height: var(--project-node-size);
                    border-radius: 50%;
                    background: var(--project-node-color);
                    border: 2px solid var(--bg1);
                    z-index: 1;
                }
                .project-name {
                    font-weight: bold;
                    color: var(--head3);
                    font-size: 1em;
                    margin-bottom: 8px;
                    line-height: 1.3;
                }
                .role {
                    font-style: italic;
                    color: var(--const);
                    margin-bottom: 12px;
                    font-size: 0.85em;
                    line-height: 1.4;
                }
                .section-title {
                    font-weight: bold;
                    color: var(--comp);
                    margin: 10px 0 5px;
                    font-size: 0.9em;
                }
                .highlights, .technologies, .achievements {
                    padding-left: 12px;
                }
                .item {
                    color: var(--base);
                    margin-bottom: 5px;
                    position: relative;
                    font-size: 0.85em;
                    line-height: 1.4;
                }
                .item::before {
                    content: '▹';
                    color: var(--green);
                    position: absolute;
                    left: -15px;
                }
                .tech-item {
                    display: inline-block;
                    background: linear-gradient(135deg, var(--green), var(--comp));
                    color: var(--bg1);
                    padding: 3px 8px;
                    border-radius: 12px;
                    margin-right: 4px;
                    margin-bottom: 4px;
                    font-size: 0.75em;
                    font-weight: bold;
                    transition: all 0.3s ease;
                    border: 1px solid var(--border);
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
                    cursor: default;
                }
                .tech-item:hover {
                    transform: translateY(-2px) scale(1.05);
                    box-shadow: 0 4px 15px rgba(0, 255, 136, 0.4);
                }

                @media (min-width: 480px) {
                    .timeline-container {
                        padding: 0 20px;
                    }

                    .experience {
                        margin-bottom: 35px;
                        padding-left: 40px;
                    }

                    .company {
                        font-size: 1.3em;
                        margin-bottom: 7px;
                    }

                    .position {
                        font-size: 1.1em;
                        margin-bottom: 5px;
                    }

                    .date {
                        font-size: 0.9em;
                        margin-bottom: 18px;
                    }

                    .project {
                        padding: 18px;
                        margin-bottom: 18px;
                        border-radius: 15px;
                    }

                    .project-name {
                        font-size: 1.05em;
                        margin-bottom: 9px;
                    }

                    .role {
                        font-size: 0.9em;
                        margin-bottom: 14px;
                    }

                    .section-title {
                        font-size: 0.95em;
                        margin: 11px 0 6px;
                    }

                    .item {
                        font-size: 0.9em;
                        margin-bottom: 6px;
                    }

                    .tech-item {
                        padding: 4px 9px;
                        font-size: 0.8em;
                        margin-right: 5px;
                        margin-bottom: 5px;
                    }
                }

                @media (min-width: 768px) {
                    .timeline::before {
                        left: 15px;
                    }
                    .experience {
                        margin-bottom: 40px;
                        padding-left: 55px;
                    }
                    .experience::before {
                        width: 28px;
                        height: 28px;
                        left: 2px;
                    }
                    .company {
                        font-size: 1.5em;
                        margin-bottom: 8px;
                    }
                    .position {
                        font-size: 1.2em;
                        margin-bottom: 6px;
                    }
                    .date {
                        font-size: 0.95em;
                        margin-bottom: 20px;
                    }
                    .projects::before {
                        left: -40px;
                    }
                    .project {
                        padding: 20px;
                        margin-bottom: 20px;
                    }
                    .project::before {
                        left: -50px;
                        width: 18px;
                        height: 18px;
                    }
                    .project-name {
                        font-size: 1.15em;
                        margin-bottom: 10px;
                    }
                    .role {
                        font-size: 0.95em;
                        margin-bottom: 15px;
                    }
                    .section-title {
                        font-size: 1em;
                        margin: 12px 0 6px;
                    }
                    .item {
                        font-size: 0.95em;
                        margin-bottom: 6px;
                    }
                    .tech-item {
                        font-size: 0.85em;
                        padding: 4px 10px;
                    }
                }

                @media (min-width: 1024px) {
                    .experience {
                        padding-left: 60px;
                    }
                    .experience::before {
                        width: 32px;
                        height: 32px;
                        left: -1px;
                    }
                    .company {
                        font-size: 1.6em;
                    }
                    .position {
                        font-size: 1.3em;
                    }
                    .date {
                        font-size: 1em;
                    }
                    .projects::before {
                        left: -45px;
                    }
                    .project::before {
                        left: -56px;
                        width: 20px;
                        height: 20px;
                    }
                    .project-name {
                        font-size: 1.2em;
                    }
                    .role, .item {
                        font-size: 1em;
                    }
                    .tech-item {
                        font-size: 0.9em;
                    }
                }
            </style>

            <div class="timeline-container">
                <div class="timeline">
                    ${data.map(exp => this.renderExperience(exp)).join('')}
                </div>
            </div>
        `;
    }

    calculateDuration(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffYears = Math.floor(diffTime / (1000 * 60 * 60 * 24 * 365));
        const diffMonths = Math.floor((diffTime % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30));

        if (diffYears === 0 && diffMonths === 0) {
            return '';
        }
        return `(${diffYears} años, ${diffMonths} meses)`;
    }

    renderExperience(exp) {
        return `
            <div class="experience">
                <div class="company">${exp.company}</div>
                <div class="position">${exp.position}</div>
                <div class="date">${exp.startDate || ''} - ${exp.endDate} ${(this.calculateDuration(exp.startDate, exp.endDate))}</div>
                <div class="projects">
                    ${exp.projects.map(proj => this.renderProject(proj)).join('')}
                </div>
            </div>
        `;
    }

    renderProject(proj) {
        return `
      <div class="project">
        <div class="project-name">${proj.name}</div>
        <div class="role">${proj.role}</div>
        <div class="highlights">
          ${proj.highlights.map(highlight => `<div class="item">${highlight}</div>`).join('')}
        </div>
        <div class="technologies">
          ${proj.technologies.map(tech => `<span class="tech-item">${tech}</span>`).join('')}
        </div>
        <div class="section-title">¿Que se logró?:</div>
        <div class="achievements">
          ${proj.achievements.map(achievement => `<div class="item">${achievement}</div>`).join('')}
        </div>
      </div>
    `;
    }
}

customElements.define('experience-timeline', ExperienceTimeline);
export default ExperienceTimeline;