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
                    --company-node-size: 20px;
                    --project-node-size: 14px;
                    --company-node-color: var(--head1);
                    --project-node-color: var(--head2);
                    --max-width: 100%;
                }
                .timeline-container {
                    max-width: var(--max-width);
                    margin: 0 auto;
                    padding: 0 10px;
                    width: 100%;
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
                    left: 9px;
                    width: var(--timeline-width);
                    background: var(--company-node-color);
                }
                .experience {
                    position: relative;
                    margin-bottom: 25px;
                    padding-left: 30px;
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
                    font-size: 1.3em;
                    font-weight: bold;
                    color: var(--head1);
                    margin-bottom: 8px;
                    line-height: 1.3;
                }
                .position {
                    font-size: 1.1em;
                    color: var(--head2);
                    margin-bottom: 6px;
                    line-height: 1.4;
                }
                .date {
                    font-style: italic;
                    color: var(--base-dim);
                    margin-bottom: 18px;
                    font-size: 0.9em;
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
                    left: -21px;
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
                    left: -27px;
                    top: 12px;
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
                    font-size: 1.1em;
                    margin-bottom: 10px;
                    line-height: 1.3;
                }
                .role {
                    font-style: italic;
                    color: var(--const);
                    margin-bottom: 14px;
                    font-size: 0.9em;
                    line-height: 1.4;
                }
                .section-title {
                    font-weight: bold;
                    color: var(--comp);
                    margin: 12px 0 6px;
                    font-size: 0.95em;
                }
                .highlights, .technologies, .achievements {
                    padding-left: 15px;
                }
                .item {
                    color: var(--base);
                    margin-bottom: 6px;
                    position: relative;
                    font-size: 0.9em;
                    line-height: 1.5;
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
                    padding: 4px 10px;
                    border-radius: 12px;
                    margin-right: 6px;
                    margin-bottom: 6px;
                    font-size: 0.8em;
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
                    :host {
                        --max-width: 600px;
                        --company-node-size: 22px;
                        --project-node-size: 15px;
                    }

                    .timeline-container {
                        padding: 0 20px;
                    }

                    .timeline::before {
                        left: 10px;
                    }

                    .experience {
                        margin-bottom: 35px;
                        padding-left: 40px;
                    }

                    .company {
                        font-size: 1.4em;
                        margin-bottom: 8px;
                    }

                    .position {
                        font-size: 1.15em;
                        margin-bottom: 6px;
                    }

                    .date {
                        font-size: 0.95em;
                        margin-bottom: 20px;
                    }

                    .projects::before {
                        left: -30px;
                    }

                    .project {
                        padding: 18px;
                        margin-bottom: 18px;
                        border-radius: 15px;
                    }

                    .project::before {
                        left: -37px;
                        top: 14px;
                    }

                    .project-name {
                        font-size: 1.15em;
                        margin-bottom: 11px;
                    }

                    .role {
                        font-size: 0.95em;
                        margin-bottom: 15px;
                    }

                    .section-title {
                        font-size: 1em;
                        margin: 13px 0 7px;
                    }

                    .item {
                        font-size: 0.95em;
                        margin-bottom: 7px;
                    }

                    .tech-item {
                        padding: 5px 11px;
                        font-size: 0.85em;
                        margin-right: 7px;
                        margin-bottom: 7px;
                    }
                }

                @media (min-width: 768px) {
                    :host {
                        --max-width: 750px;
                        --company-node-size: 26px;
                        --project-node-size: 18px;
                    }

                    .timeline::before {
                        left: 12px;
                    }

                    .experience {
                        margin-bottom: 40px;
                        padding-left: 50px;
                    }

                    .experience::before {
                        width: var(--company-node-size);
                        height: var(--company-node-size);
                        left: -1px;
                    }

                    .company {
                        font-size: 1.5em;
                        margin-bottom: 9px;
                    }

                    .position {
                        font-size: 1.25em;
                        margin-bottom: 7px;
                    }

                    .date {
                        font-size: 1em;
                        margin-bottom: 22px;
                    }

                    .projects::before {
                        left: -37px;
                    }

                    .project {
                        padding: 20px;
                        margin-bottom: 20px;
                    }

                    .project::before {
                        left: -46px;
                        width: var(--project-node-size);
                        height: var(--project-node-size);
                        top: 15px;
                    }

                    .project-name {
                        font-size: 1.2em;
                        margin-bottom: 12px;
                    }

                    .role {
                        font-size: 1em;
                        margin-bottom: 16px;
                    }

                    .section-title {
                        font-size: 1.05em;
                        margin: 14px 0 8px;
                    }

                    .item {
                        font-size: 1em;
                        margin-bottom: 7px;
                    }

                    .tech-item {
                        font-size: 0.9em;
                        padding: 5px 12px;
                        margin-right: 8px;
                        margin-bottom: 8px;
                    }
                }

                @media (min-width: 1024px) {
                    :host {
                        --max-width: 800px;
                        --company-node-size: 30px;
                        --project-node-size: 20px;
                    }

                    .timeline::before {
                        left: 14px;
                    }

                    .experience {
                        padding-left: 60px;
                    }

                    .experience::before {
                        width: var(--company-node-size);
                        height: var(--company-node-size);
                        left: -1px;
                    }

                    .company {
                        font-size: 1.6em;
                    }

                    .position {
                        font-size: 1.3em;
                    }

                    .date {
                        font-size: 1.05em;
                    }

                    .projects::before {
                        left: -45px;
                    }

                    .project::before {
                        left: -56px;
                        width: var(--project-node-size);
                        height: var(--project-node-size);
                    }

                    .project-name {
                        font-size: 1.25em;
                    }

                    .role, .item {
                        font-size: 1.05em;
                    }

                    .tech-item {
                        font-size: 0.95em;
                        padding: 6px 14px;
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
        if (!startDate || !endDate) return '';

        const start = new Date(startDate);
        const end = new Date(endDate);

        if (isNaN(start.getTime()) || isNaN(end.getTime())) return '';

        const diffTime = Math.abs(end - start);
        const diffYears = Math.floor(diffTime / (1000 * 60 * 60 * 24 * 365));
        const diffMonths = Math.floor((diffTime % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30));

        if (diffYears === 0 && diffMonths === 0) {
            return '';
        }

        const yearString = diffYears > 0 ? `${diffYears} año${diffYears > 1 ? 's' : ''}` : '';
        const monthString = diffMonths > 0 ? `${diffMonths} mes${diffMonths > 1 ? 'es' : ''}` : '';

        if (yearString && monthString) {
            return `(${yearString}, ${monthString})`;
        } else {
            return `(${yearString}${monthString})`;
        }
    }

    renderExperience(exp) {
        const dateString = this.formatDateRange(exp.startDate, exp.endDate);
        const duration = this.calculateDuration(exp.startDate, exp.endDate);

        return `
            <div class="experience">
                <div class="company">${exp.company}</div>
                <div class="position">${exp.position}</div>
                <div class="date">${dateString} ${duration}</div>
                <div class="projects">
                    ${exp.projects.map(proj => this.renderProject(proj)).join('')}
                </div>
            </div>
        `;
    }

    formatDateRange(startDate, endDate) {
        const start = startDate ? new Date(startDate) : null;
        const end = endDate ? new Date(endDate) : null;

        if (!start && !end) return '';

        const formatDate = (date) => {
            if (!date || isNaN(date.getTime())) return '';
            return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long' });
        };

        const startString = formatDate(start);
        const endString = end ? formatDate(end) : 'Presente';

        if (startString && endString) {
            return `${startString} - ${endString}`;
        } else if (startString) {
            return `Desde ${startString}`;
        } else if (endString !== 'Presente') {
            return `Hasta ${endString}`;
        }

        return '';
    }

    renderProject(proj) {
        proj.achievements = [];
        proj.technologies = [];
        proj.highlights = [];

        return  `
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