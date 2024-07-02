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
          --company-node-color: var(--blue);
          --project-node-color: var(--green);
          --max-width: 800px;
        }
        .timeline-container {
          max-width: var(--max-width);
          margin: 0 auto;
          padding: 0 20px;
        }
        .timeline {
          position: relative;
          padding: 20px 0;
        }
        .timeline::before {
          content: '';
          position: absolute;
          top: 0;
          bottom: 0;
          left: 11px;
          width: var(--timeline-width);
          background: var(--company-node-color);
        }
        .experience {
          position: relative;
          margin-bottom: 40px;
          padding-left: 40px;
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
          font-size: 1.4em;
          font-weight: bold;
          color: var(--head1);
          margin-bottom: 8px;
        }
        .position {
          font-size: 1.2em;
          color: var(--head2);
          margin-bottom: 5px;
        }
        .date {
          font-style: italic;
          color: var(--base-dim);
          margin-bottom: 20px;
          font-size: 0.9em;
        }
        .projects {
          position: relative;
        }
        .projects::before {
          content: '';
          position: absolute;
          top: 0;
          bottom: 0;
          left: -31px;
          width: var(--timeline-width);
          background: var(--project-node-color);
        }
        .project {
          background: var(--head2-bg);
          border-radius: 10px;
          padding: 15px;
          margin-bottom: 20px;
          position: relative;
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
          font-size: 1.1em;
          margin-bottom: 10px;
        }
        .role {
          font-style: italic;
          color: var(--const);
          margin-bottom: 10px;
          font-size: 0.9em;
        }
        .section-title {
          font-weight: bold;
          color: var(--comp);
          margin: 12px 0 6px;
          font-size: 1em;
        }
        .highlights, .technologies, .achievements {
          padding-left: 15px;
        }
        .item {
          color: var(--base);
          margin-bottom: 6px;
          position: relative;
          font-size: 0.9em;
        }
        .item::before {
          content: '▹';
          color: var(--green);
          position: absolute;
          left: -15px;
        }
        .tech-item {
          display: inline-block;
          background-color: var(--green);
          color: var(--bg1);
          padding: 2px 5px;
          border-radius: 5px;
          margin-right: 5px;
          margin-bottom: 5px;
          font-size: 0.8em;
        }
        
        @media (min-width: 768px) {
          .timeline::before {
            left: 15px;
          }
          .experience {
            padding-left: 60px;
          }
          .experience::before {
            width: 32px;
            height: 32px;
            left: 0;
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
          .project {
            padding: 20px;
          }
          .project::before {
            left: -51px;
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

    renderExperience(exp) {
        return `
      <div class="experience">
        <div class="company">${exp.company}</div>
        <div class="position">${exp.position}</div>
        <div class="date">${exp.startDate || ''} - ${exp.endDate} (${exp.duration})</div>
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
        <div class="section-title">Highlights:</div>
        <div class="highlights">
          ${proj.highlights.map(highlight => `<div class="item">${highlight}</div>`).join('')}
        </div>
        <div class="section-title">Technologies:</div>
        <div class="technologies">
          ${proj.technologies.map(tech => `<span class="tech-item">${tech}</span>`).join('')}
        </div>
        <div class="section-title">Achievements:</div>
        <div class="achievements">
          ${proj.achievements.map(achievement => `<div class="item">${achievement}</div>`).join('')}
        </div>
      </div>
    `;
    }
}

customElements.define('experience-timeline', ExperienceTimeline);
export default ExperienceTimeline;