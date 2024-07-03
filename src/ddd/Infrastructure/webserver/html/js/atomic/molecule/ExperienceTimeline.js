class ExperienceTimeline extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
    }

    connectedCallback() {
        this.render();
    }

    render() {
        const data = JSON.parse(this.getAttribute('data') || '[]');


        this.assertions(data);

        this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          --timeline-width: 3px;
          --company-node-size: 24px;
          --project-node-size: 16px;
          --company-node-color: var(--aqua);
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
          padding: 30px 0;
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
          margin-bottom: 60px;
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
          background: var(--meta);
          border: 3px solid #292b2e;
          box-shadow: 0 0 0 5px rgba(79, 151, 215, 0.3);
          z-index: 1;
        }
        .company {
          font-size: 1.5em;
          font-weight: bold;
          color: #4f97d7;
          margin-bottom: 8px;
        }
        .position {
          font-size: 1.2em;
          color: #2d9574;
          margin-bottom: 5px;
        }
        .date {
          font-style: italic;
          color: #686868;
          margin-bottom: 20px;
          font-size: 0.95em;
        }
        .project {
          background: var(--head2-bg)
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 30px;
          margin-left: 20px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.2);
          transition: all 0.3s ease;
        }
        .projects {
            margin-left: 0;
            margin-bottom: 30px;
        
        }
        .project:hover {
          transform: translateY(-5px);
          box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
        .project-name {
          font-weight: bold;
          color: #67b11d;
          font-size: 1.2em;
          margin-bottom: 12px;
        }
        .role {
          font-style: italic;
          color: #a45bad;
          margin-bottom: 10px;
        }
        .section-title {
          font-weight: bold;
          color: #c56ec3;
          margin: 15px 0 8px;
        }
        .highlights, .technologies, .achievements {
          padding-left: 20px;
        }
        .item {
          color: var(--head2);
          margin-bottom: 8px;
          position: relative;
          font-size: 0.95em;
        }
        .item::before {
          content: '▹';
          color: #67b11d;
          position: absolute;
          left: -20px;
        }
        
        @media (min-width: 768px) {
          .timeline-container {
            padding: 0 40px;
          }
          .timeline::before {
            left: 12px;
          }
          .experience {
            padding-left: 50px;
          }
          .company {
            font-size: 1.7em;
          }
          .position {
            font-size: 1.3em;
          }
          .date {
            font-size: 1em;
          }
          .project {
            padding: 25px;
            margin-left: 0;
          }
          .project-name {
            font-size: 1.3em;
          }
          .item {
            font-size: 1em;
          }
          .tech-item {
          /* little square to show all in a row */
            display: inline-block;
            background-color: var(--comment-bg);
            color: var(--comment);
            padding: 2px 5px;
            border-radius: 5px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 0.9em;
            
          }
          .timeline-title {
            font-size: 2em;
            text-align: center;
            margin-bottom: 30px;
            color: var(--head2);
            
            

            
            
          
        }
      </style>
      
        <h2 class="timeline-title">Experience And Projects</h2>
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
        <div class="date">${exp.startDate} - ${exp.endDate} (${exp.duration})</div>
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
          ${proj.technologies.map(tech => `<div class="tech-item">${tech}</div>`).join('')}
        </div>
        <div class="section-title">Achievements:</div>
        <div class="achievements">
          ${proj.achievements.map(achievement => `<div class="item">${achievement}</div>`).join('')}
        </div>
      </div>
    `;
    }


    assertions = data => {
        let someError = "";
        data.forEach(experience => {
            const {company, position, endDate, projects, startDate} = experience;

            if (!company) {
                someError += 'Company is missing in experience object, ';
            }
            if (!position) {
                someError += 'Position is missing in experience object, ';
            }
            if (!startDate) {
                someError += 'StartDate is missing in experience object, ';
            }
            if (!endDate) {
                someError += 'EndDate is missing in experience object, ';
            }
            if (!projects) {
                someError += 'Projects is missing in experience object, ';
            }
            projects.forEach(
                proj => {
                    const {achievements, role, technologies, highlights, name} = proj;
                    if (!name) {
                        someError += 'Name is missing in project object, ';
                    }
                    if (!role) {
                    }
                    if (!highlights) {
                        someError += 'Highlights is missing in project object, ';
                    }
                    if (!technologies) {
                        someError += 'Technologies is missing in project object, ';
                    }
                    if (!achievements) {
                        someError += 'Achievements is missing in project object, ';
                    }
                }
            );
        })

        if ("" !== someError) {
            const exampleExperience = {
                "company": "FooBar Inc.",
                "position": "Lead Developer in FooBar Solutions",
                "startDate": "2015",
                "endDate": "Present",
                "projects": [
                    {
                        "name": "FooBar Project",
                        "role": "Project Lead",
                        "highlights": [
                            "Developed and maintained a FooBar web application using FooScript and BarJS frameworks.",
                            "Designed and executed a scalable microservices architecture using FooServices and BarContainers."
                        ],
                        "technologies": [
                            "FooScript", "BarJS", "FooPay API", "BarChat API", "FooCI", "BarDeploy", "FooDesign", "BarTesting", "FooServices", "BarContainers"
                        ],
                        "achievements": [
                            "Increased the performance of the FooBar web application by 30%.",
                            "Reduced the time to deploy new features by 20%"
                        ]
                    }
                ]
            };
            console.error(someError + "Example experience object: ", exampleExperience);
        }
    };
}

customElements.define('experience-timeline', ExperienceTimeline);
export default ExperienceTimeline